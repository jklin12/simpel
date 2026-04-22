<?php

namespace App\Http\Controllers\Admin\Surat;

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
        $filters = $request->only(['search', 'status', 'jenis_surat_id', 'sort']);

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
     * Show the form for editing the specified permohonan.
     */
    public function edit($id)
    {
        try {
            $permohonanSurat = $this->service->getPermohonanById($id);

            // Check if still editable
            if (in_array($permohonanSurat->status, ['completed', 'rejected'])) {
                return redirect()->route('admin.permohonan-surat.show', $id)
                    ->with('error', 'Permohonan sudah diproses dan tidak bisa diubah.');
            }

            return view('admin.permohonan-surat.edit', compact('permohonanSurat'));
        } catch (\Exception $e) {
            return redirect()->route('admin.permohonan-surat.index')
                ->with('error', 'Permohonan tidak ditemukan');
        }
    }

    /**
     * Update the specified permohonan in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pemohon'   => 'required|string|max:255',
            'nik_pemohon'    => 'required|string|size:16',
            'phone_pemohon'  => 'required|string|max:20',
            'alamat_pemohon' => 'required|string',
            'keperluan'      => 'required|string',
            'data_permohonan' => 'nullable|array',
        ]);

        try {
            $this->service->updateDataPermohonan($id, $request->only([
                'nama_pemohon',
                'nik_pemohon',
                'phone_pemohon',
                'alamat_pemohon',
                'keperluan',
                'data_permohonan'
            ]));

            return redirect()->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Data pemohon berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
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
            $permohonan->load(['jenisSurat', 'kelurahan.kecamatan', 'dokumens']);
            $kelurahan = $permohonan->kelurahan;

            // Prepare Pas Foto for SPN (Surat Pengantar Nikah)
            $pasFotoBase64 = null;
            if (strtolower($permohonan->jenisSurat->kode ?? '') === 'spn') {
                $pasFoto = $permohonan->dokumens
                    ->where('jenis_dokumen', 'skmh_pas_foto')
                    ->whereIn('mime_type', ['image/jpeg', 'image/jpg'])
                    ->first();

                if ($pasFoto && Storage::disk('public')->exists($pasFoto->file_path)) {
                    $pasFotoBase64 = base64_encode(Storage::disk('public')->get($pasFoto->file_path));
                }
            }

            // Data lurah dari kelurahan DB
            $lurah = [
                'nama'  => $kelurahan->lurah_nama ? strtoupper($kelurahan->lurah_nama) : 'KEPALA KELURAHAN',
                'nip'   => $kelurahan->lurah_nip ?? '-',
                'title' => $kelurahan->signer_title,
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
            $trackUrl = route('layanan.surat.tracking.search', ['track_token' => $permohonan->track_token]);
            $qrBase64 = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(120)
                    ->margin(1)
                    ->generate($trackUrl)
            );

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($pdfView, [
                'permohonan'    => $permohonan,
                'kelurahan'     => $kelurahan,
                'lurah'         => $lurah,
                'qrBase64'      => $qrBase64,
                'pasFotoBase64' => $pasFotoBase64,
            ]);

            $pdf->setPaper('a4', 'portrait');

            $filename = ($permohonan->nomor_surat)
                ? str_replace('/', '-', $permohonan->nomor_surat) . '.pdf'
                : $permohonan->nomor_permohonan . '.pdf';

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            dd($e); 
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
            'signed_letter' => 'required|file|mimes:pdf|max:10240',
        ], [
            'signed_letter.required' => 'File surat yang sudah ditandatangani wajib diupload.',
            'signed_letter.mimes'    => 'File harus berformat PDF.',
            'signed_letter.max'      => 'Ukuran file maksimal 10MB.',
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
    /**
     * Delete the specified permohonan.
     */
    public function destroy($id)
    {
        // Check permission
        if (!Auth::user()->can('delete_permohonan')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        try {
            $this->service->deletePermohonan($id);

            return redirect()
                ->route('admin.permohonan-surat.index')
                ->with('success', 'Permohonan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reset permohonan status to pending.
     * Only available to Super Admin.
     */
    public function resetStatus($id)
    {
        // Check if user is Super Admin
        if (!Auth::user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Hanya Super Admin yang dapat me-reset status permohonan.');
        }

        try {
            $this->service->resetPermohonanStatus($id);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Status permohonan berhasil di-reset ke Pending.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal reset status: ' . $e->getMessage());
        }
    }
}
