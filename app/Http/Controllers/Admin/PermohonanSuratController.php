<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovePermohonanRequest;
use App\Http\Requests\RejectPermohonanRequest;
use App\Services\PermohonanSuratService;
use App\Models\JenisSurat;
use App\Models\PermohonanDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermohonanSuratController extends Controller
{
    protected $service;

    public function __construct(PermohonanSuratService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of permohonan based on user role and location.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['search', 'status', 'jenis_surat_id']);

        $permohonanSurats = $this->service->getPermohonanByUserRole($user, $filters, 15);
        $jenisSurats = JenisSurat::where('is_active', true)->orderBy('nama')->get();

        return view('admin.permohonan-surat.index', compact('permohonanSurats', 'jenisSurats'));
    }

    /**
     * Display the specified permohonan with approval timeline.
     */
    public function show($id)
    {
        try {
            $permohonanSurat = $this->service->getPermohonanById($id);
            $permohonanSurat->load('dokumens'); // Eager load documents
            $approvals = $permohonanSurat->approvals()->orderBy('step_order')->get();

            return view('admin.permohonan-surat.show', compact('permohonanSurat', 'approvals'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.permohonan-surat.index')
                ->with('error', 'Permohonan tidak ditemukan');
        }
    }

    /**
     * Approve the permohonan at current step.
     */
    public function approve(ApprovePermohonanRequest $request, $id)
    {
        try {
            $this->service->approvePermohonan($id, Auth::id(), $request->catatan);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Permohonan berhasil disetujui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the permohonan.
     */
    public function reject(RejectPermohonanRequest $request, $id)
    {
        try {
            $this->service->rejectPermohonan($id, Auth::id(), $request->rejected_reason);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Permohonan telah ditolak');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Download generated letter as PDF.
     */
    public function downloadLetter($id)
    {
        try {
            $permohonan = $this->service->getPermohonanById($id);

            if (!in_array($permohonan->status, ['approved', 'completed'])) {
                return redirect()
                    ->back()
                    ->with('error', 'Surat belum selesai diproses');
            }

            // Load relations
            $permohonan->load(['jenisSurat', 'kelurahan.kecamatan']);
            $kelurahan = $permohonan->kelurahan;

            // Data lurah dari kelurahan DB
            $lurah = [
                'nama' => $kelurahan->lurah_nama ? strtoupper($kelurahan->lurah_nama) : 'KEPALA KELURAHAN',
                'nip'  => $kelurahan->lurah_nip ?? '-',
            ];

            // Pilih template PDF berdasarkan kode jenis surat
            $kode = strtolower($permohonan->jenisSurat->kode ?? '');
            $pdfView = "pdf.{$kode}";

            if (!\Illuminate\Support\Facades\View::exists($pdfView)) {
                return redirect()
                    ->back()
                    ->with('info', "Template PDF untuk surat {$permohonan->jenisSurat->nama} belum tersedia.");
            }

            // Generate QR code sebagai base64 PNG untuk embed di PDF
            $trackUrl = route('tracking.search', ['track_token' => $permohonan->track_token]);
            $qrBase64 = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(120)
                    ->margin(1)
                    ->generate($trackUrl)
            );

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($pdfView, [
                'permohonan' => $permohonan,
                'kelurahan'  => $kelurahan,
                'lurah'      => $lurah,
                'qrBase64'   => $qrBase64,
            ]);

            $pdf->setPaper('a4', 'portrait');

            $filename = ($permohonan->nomor_surat)
                ? str_replace('/', '-', $permohonan->nomor_surat) . '.pdf'
                : $permohonan->nomor_permohonan . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Upload signed PDF letter — transitions status from 'approved' to 'completed'.
     */
    public function uploadSignedLetter(Request $request, $id)
    {
        $request->validate([
            'signed_letter' => 'required|file|mimes:pdf|max:5120',
        ], [
            'signed_letter.required' => 'File surat yang sudah ditandatangani wajib diupload.',
            'signed_letter.mimes'    => 'File harus berformat PDF.',
            'signed_letter.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $permohonan = $this->service->getPermohonanById($id);

            if ($permohonan->status !== 'approved') {
                return redirect()->back()->with('error', 'Permohonan tidak dalam status menunggu TTD.');
            }

            $path = $request->file('signed_letter')
                ->store('surat-selesai/' . $id, 'public');

            $permohonan->update([
                'signed_file_path' => $path,
                'status'           => 'completed',
                'completed_at'     => now(),
            ]);

            // Kirim WA notifikasi completed ke pemohon
            try {
                $permohonan->fresh()->notify(
                    new \App\Notifications\PermohonanApprovedWhatsapp($permohonan->fresh())
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('WA completed notification failed: ' . $e->getMessage());
            }

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Surat berhasil diupload. Status permohonan sekarang Selesai dan notifikasi telah dikirim ke pemohon.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal upload surat: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific document attachment.
     */
    public function downloadDokumen($id, $dokumenId)
    {
        try {
            $dokumen = PermohonanDokumen::where('permohonan_surat_id', $id)
                ->findOrFail($dokumenId);

            if (!Storage::disk('public')->exists($dokumen->file_path)) {
                return redirect()
                    ->back()
                    ->with('error', 'File tidak ditemukan di server');
            }

            return Storage::disk('public')->download(
                $dokumen->file_path,
                $dokumen->original_name
            );
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Dokumen tidak ditemukan');
        }
    }
}
