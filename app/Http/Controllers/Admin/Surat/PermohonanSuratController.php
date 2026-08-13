<?php

namespace App\Http\Controllers\Admin\Surat;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovePermohonanRequest;
use App\Http\Requests\RejectPermohonanRequest;
use App\Http\Requests\RequestPerubahanRequest;
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
            $permohonanSurat->load('dokumens', 'revisiRequests.requestedBy', 'revisiRequests.reviewedBy');
            $approvals = $permohonanSurat->approvals()->orderBy('step_order')->get();
            $revisiRequests = $permohonanSurat->revisiRequests()->latest()->get();

            return view('admin.permohonan-surat.show', compact('permohonanSurat', 'approvals', 'revisiRequests'));
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

            // Check if still editable — whitelist allowed statuses
            if (!in_array($permohonanSurat->status, ['draft', 'pending', 'in_review', 'revision_open'])) {
                return redirect()->route('admin.permohonan-surat.show', $id)
                    ->with('error', 'Permohonan tidak dapat diubah pada status saat ini.');
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

                if ($pasFoto && Storage::disk('local')->exists($pasFoto->file_path)) {
                    $pasFotoBase64 = base64_encode(Storage::disk('local')->get($pasFoto->file_path));
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

            // Generate filename: gunakan nomor_surat jika ada, atau nomor_permohonan dengan DRAFT prefix
            if ($permohonan->nomor_surat) {
                $filename = str_replace('/', '-', $permohonan->nomor_surat) . '.pdf';
            } else {
                $filename = 'DRAFT_' . str_replace('/', '-', $permohonan->nomor_permohonan) . '.pdf';
            }

            return $pdf->stream($filename);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal generate PDF surat', [
                'permohonan_id' => $id,
                'error'         => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Gagal generate PDF surat. Silakan hubungi administrator jika masalah berlanjut.');
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

            // Disk privat (local) — surat final berisi nama + NIK + alamat warga.
            $path = $request->file('signed_letter')
                ->store('surat-selesai/' . $id, 'local');

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
        // Enforce wilayah-based ownership (mencegah IDOR) sebelum menyajikan file.
        $this->service->getPermohonanById($id);

        try {
            $dokumen = PermohonanDokumen::where('permohonan_surat_id', $id)
                ->findOrFail($dokumenId);

            if (!Storage::disk('local')->exists($dokumen->file_path)) {
                return redirect()
                    ->back()
                    ->with('error', 'File tidak ditemukan di server');
            }

            return Storage::disk('local')->download(
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
     * Preview a specific document attachment inline (for the modal viewer), not force-download.
     */
    public function previewDokumen($id, $dokumenId)
    {
        // Enforce wilayah-based ownership (mencegah IDOR) sebelum menyajikan file.
        $this->service->getPermohonanById($id);

        try {
            $dokumen = PermohonanDokumen::where('permohonan_surat_id', $id)
                ->findOrFail($dokumenId);

            if (!Storage::disk('local')->exists($dokumen->file_path)) {
                abort(404, 'File tidak ditemukan di server');
            }

            return Storage::disk('local')->response(
                $dokumen->file_path,
                $dokumen->original_name
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Dokumen tidak ditemukan');
        }
    }

    /**
     * Download the signed final letter from the private disk.
     * Di-scope otorisasi wilayah lewat getPermohonanById (mencegah IDOR),
     * karena file kini di disk privat dan tidak lagi web-accessible.
     */
    public function downloadSignedLetter($id)
    {
        // Enforce wilayah-based ownership sebelum menyajikan file.
        $permohonan = $this->service->getPermohonanById($id);

        if (!$permohonan || !$permohonan->signed_file_path
            || !Storage::disk('local')->exists($permohonan->signed_file_path)) {
            return redirect()->back()->with('error', 'File surat tidak ditemukan di server.');
        }

        $filename = str_replace('/', '-', $permohonan->nomor_surat ?? $permohonan->nomor_permohonan) . '.pdf';

        return Storage::disk('local')->download($permohonan->signed_file_path, $filename);
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

    /**
     * Retry WhatsApp notification for failed attempts.
     */
    public function retryWhatsapp($id)
    {
        try {
            $permohonan = $this->service->getPermohonanById($id);

            $failedLog = \App\Models\WhatsappNotificationLog::where('permohonan_id', $id)
                ->where('status', 'failed')
                ->latest()
                ->first();

            if (!$failedLog) {
                return redirect()->back()->with('error', 'Tidak ada log notifikasi yang gagal untuk di-retry.');
            }

            $notification = match($failedLog->notification_type) {
                'created' => new \App\Notifications\PermohonanCreatedWhatsapp($permohonan),
                'approved' => new \App\Notifications\PermohonanApprovedWhatsapp($permohonan),
                'rejected' => new \App\Notifications\PermohonanRejectedWhatsapp($permohonan, $permohonan->rejected_reason ?? 'Tidak sesuai kriteria'),
                'revisi' => new \App\Notifications\PermohonanRevisiWhatsapp($permohonan),
                'sign_request' => new \App\Notifications\PermohonanSignRequestWhatsapp(
                    $permohonan,
                    $permohonan->currentApprovalStep?->approval_pejabat_name ?? 'Bapak/Ibu'
                ),
                default => throw new \Exception('Tipe notifikasi tidak dikenal: ' . $failedLog->notification_type)
            };

            $permohonan->notify($notification);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Notifikasi WhatsApp sedang dikirim ulang ke antrian.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal retry notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Admin kelurahan mengajukan request perubahan untuk surat yang sudah approved.
     */
    public function requestPerubahan(RequestPerubahanRequest $request, $id)
    {
        if (!Auth::user()->hasRole(['admin_kelurahan', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengajukan request perubahan.');
        }

        try {
            $this->service->requestPerubahan($id, Auth::id(), $request->alasan);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Request perubahan berhasil diajukan. Menunggu persetujuan admin kecamatan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin kecamatan/super admin menyetujui request perubahan.
     */
    public function approveRevisiRequest(Request $request, $id)
    {
        if (!Auth::user()->hasRole(['admin_kecamatan', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyetujui request perubahan.');
        }

        try {
            $this->service->approveRevisiRequest($id, Auth::id(), $request->catatan);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Request perubahan disetujui. Admin kelurahan sekarang dapat mengedit data.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin kecamatan/super admin menolak request perubahan.
     */
    public function rejectRevisiRequest(Request $request, $id)
    {
        $request->validate(['catatan_reviewer' => 'required|string|max:500']);

        if (!Auth::user()->hasRole(['admin_kecamatan', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menolak request perubahan.');
        }

        try {
            $this->service->rejectRevisiRequest($id, Auth::id(), $request->catatan_reviewer);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Request perubahan ditolak. Status surat kembali ke Disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin kelurahan mengkonfirmasi selesai edit dari status revision_open.
     */
    public function confirmEditDone($id)
    {
        if (!Auth::user()->hasRole(['admin_kelurahan', 'super_admin'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        try {
            $this->service->confirmEditDone($id, Auth::id());

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Edit selesai. Status permohonan kembali ke Disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Re-run full OCR verification process from scratch.
     * Only available to Super Admin, and only for permohonan with OCR rules configured.
     */
    public function retryOcrVerification($id)
    {
        if (!Auth::user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Hanya Super Admin yang dapat menjalankan ulang verifikasi OCR.');
        }

        try {
            $permohonan = $this->service->getPermohonanById($id);

            // Check if jenis_surat has OCR rules configured
            if (!$permohonan->jenisSurat->ocr_rules || count($permohonan->jenisSurat->ocr_rules) === 0) {
                return redirect()->back()->with('error', 'Jenis surat ini tidak memiliki aturan verifikasi OCR yang dikonfigurasi.');
            }

            $permohonan->update([
                'ocr_status' => 'pending',
                'ai_insight' => null,
            ]);

            \App\Jobs\VerifyPermohonanDocuments::dispatch($permohonan->id);

            return redirect()
                ->route('admin.permohonan-surat.show', $id)
                ->with('success', 'Proses verifikasi OCR dimulai ulang. Harap tunggu beberapa menit...');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menjalankan ulang verifikasi OCR: ' . $e->getMessage());
        }
    }
}
