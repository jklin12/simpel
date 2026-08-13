<?php

namespace App\Http\Controllers\Layanan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Http\Requests\StorePermohonanRequest;
use App\Models\PermohonanSurat;
use App\Models\PermohonanApproval;
use App\Models\PermohonanDokumen;
use App\Models\ApprovalFlow;
use App\Services\PermohonanSuratService;
use App\Services\Ocr\KtpOcrService;
use App\Services\Ai\Exceptions\AiProviderException;
use App\Jobs\VerifyPermohonanDocuments;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\PermohonanBaruNotification;
use App\Notifications\PermohonanCreatedWhatsapp;
use Illuminate\Support\Facades\Log;

class PermohonanController extends Controller
{
    protected KtpOcrService $ktpOcr;

    public function __construct(KtpOcrService $ktpOcr)
    {
        $this->ktpOcr = $ktpOcr;
    }

    /**
     * Show the application form.
     */
    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');
        $kelurahanId = $request->query('kelurahan_id');

        //dd($serviceId,$kelurahanId);
        if (!$serviceId || !$kelurahanId) {
            return redirect()->route('services.index')->with('error', 'Silakan pilih layanan dan lokasi terlebih dahulu.');
        }

        $service = JenisSurat::findOrFail($serviceId);
        $kelurahan = Kelurahan::findOrFail($kelurahanId);

        // Piloting restriction
        /*if ($kelurahanId != '6372010006') {
            return redirect()->route('layanan.index')->with('error', 'Layanan saat ini hanya tersedia untuk wilayah piloting (Syamsudin Noor).');
        }*/

        $pekerjaanList = \App\Models\Pekerjaan::orderBy('nama')->pluck('nama')->toArray();

        return view('user.permohonan.create_public', compact('service', 'kelurahan', 'pekerjaanList'));
    }

    /**
     * Store the application.
     */
    public function store(StorePermohonanRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $trackToken = strtoupper(Str::random(10));
            $service = JenisSurat::findOrFail($request->jenis_surat_id);

            // Evaluate pemohon data based on jenis_surat_id
            $namaPemohon = '';
            $nikPemohon = '';
            $phonePemohon = '';
            $alamatPemohon = '';

            switch (strtoupper($service->kode)) {
                case 'SKM':
                    $namaPemohon = $request->nama_pelapor;
                    $nikPemohon = $request->nik_pelapor;
                    $phonePemohon = $request->no_wa;
                    $alamatPemohon = $request->alamat_pelapor ?? '';
                    break;
                case 'SKDKO':
                    $namaPemohon = $request->nama_lengkap;
                    $nikPemohon = $request->nik_bersangkutan;
                    $phonePemohon = $request->no_wa;
                    $alamatPemohon = $request->alamat_jalan ?? '-';
                    break;
                case 'SKTM':
                case 'SKTMR':
                case 'SKBM':
                default:
                    $namaPemohon = $request->nama_lengkap;
                    $nikPemohon = $request->nik_bersangkutan;
                    $phonePemohon = $request->no_wa;
                    $alamatPemohon = $request->alamat_lengkap;
                    break;
            }

            // Verification: Same NIK cannot submit the same Jenis Surat within the same day
            $today = now()->startOfDay();
            $existing = PermohonanSurat::where('nik_pemohon', $nikPemohon)
                ->where('jenis_surat_id', $request->jenis_surat_id)
                ->where('created_at', '>=', $today)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->with('error', 'NIK ' . $nikPemohon . ' sudah mengajukan permohonan ' . $service->nama . ' hari ini. Silakan coba lagi besok atau gunakan fitur lacak status jika sudah ada token.')
                    ->withInput();
            }

            // Filter out non-data fields AND file fields for JSON storage
            $excludeFields = array_merge(
                ['_token', 'jenis_surat_id', 'kelurahan_id'],
                StorePermohonanRequest::fileFields()
            );
            $rawData = $request->except($excludeFields);

            // Sanitize numeric fields (remove thousands separator dots)
            if (isset($rawData['jumlah_penghasilan'])) {
                $rawData['jumlah_penghasilan'] = str_replace('.', '', $rawData['jumlah_penghasilan']);
            }

            $dataPermohonan = array_map(fn($v) => is_string($v) ? strtoupper($v) : $v, $rawData);

            // Generate Nomor Permohonan: REG/YYYYMMDD/RANDOM
            $nomorPermohonan = 'REG/' . date('Ymd') . '/' . strtoupper(Str::random(5));

            // Create Permohonan
            $permohonan = PermohonanSurat::create([
                'track_token' => $trackToken,
                'nomor_permohonan' => $nomorPermohonan,
                'jenis_surat_id' => $request->jenis_surat_id,
                'kelurahan_id' => $request->kelurahan_id,
                'created_by_user_id' => auth()->id(), // Nullable now in migration
                'nama_pemohon' => $namaPemohon,
                'nik_pemohon' => $nikPemohon,
                'phone_pemohon' => $phonePemohon,
                'alamat_pemohon' => $alamatPemohon,
                'data_permohonan' => $dataPermohonan, // Casted to array/json in model
                'keperluan' => 'Permohonan ' . $service->nama,
                'status' => 'pending',
                'current_step' => 1,
            ]);

            // Handle File Uploads
            $this->handleFileUploads($request, $permohonan);

            // Trigger Approval Flow
            // Surat tingkat Kecamatan: langsung ke admin_kecamatan
            if (in_array(strtoupper($service->kode), ['SDNH', 'SKDK', 'ROIPK'])) {
                $targetRole = 'admin_kecamatan';
                $stepName   = 'Verifikasi Tingkat Kecamatan';
            } else {
                $targetRole = 'admin_kelurahan';
                $stepName   = 'Verifikasi Berkas';
            }

            PermohonanApproval::create([
                'permohonan_surat_id' => $permohonan->id,
                'target_role'         => $targetRole,
                'step_name'           => $stepName,
                'step_order'          => 1,
                'status'              => 'pending',
            ]);

            // Notify Admins
            $adminKecamatan = \App\Models\User::role('admin_kecamatan')->get();
            $superAdmins    = \App\Models\User::role('super_admin')->get();

            $notifiableAdmins = collect();

            // Jika BUKAN surat tingkat kecamatan, admin kelurahan diikutkan notifikasinya
            if (!in_array(strtoupper($service->kode), ['SDNH', 'SKDK', 'ROIPK'])) {
                $adminKelurahan = \App\Models\User::role(['admin_kelurahan', 'lurah'])
                    ->where('kelurahan_id', $request->kelurahan_id)
                    ->get();
                $notifiableAdmins = $notifiableAdmins->merge($adminKelurahan);
            }

            $notifiableAdmins = $notifiableAdmins->merge($adminKecamatan)->merge($superAdmins)->unique('id');

            foreach ($notifiableAdmins as $admin) {
                $admin->notify(new PermohonanBaruNotification($permohonan));
            }

            // Notify Applicant (WhatsApp)
            try {
                $permohonan->notify(new PermohonanCreatedWhatsapp($permohonan));
            } catch (\Exception $e) {
                // Log error but don't fail the transaction
                Log::error('WA Notification failed: ' . $e->getMessage());
            }

            // Notify Admins (WhatsApp)
            try {
                // Notify actual application admins (excluding Lurah which uses master data)
                foreach ($notifiableAdmins as $admin) {
                    if (!$admin->hasRole(['lurah', 'camat'])) {
                        $admin->notify(new PermohonanCreatedWhatsapp($permohonan));
                    }
                }

                // Determine Pejabat Master Data HP
                $jenisSurat = strtoupper($service->kode ?? '');
                $permohonanContext = \App\Models\PermohonanSurat::with('kelurahan.kecamatan')->find($permohonan->id);

                if ($jenisSurat === 'SDNH') {
                    $pejabatHp = $permohonanContext->kelurahan->kecamatan->camat_no_hp ?? null;
                    $namaPejabat = 'Bapak/Ibu Camat';
                }

                if (!empty($pejabatHp)) {
                    // Send via On-Demand Notification
                    \Illuminate\Support\Facades\Notification::route('whatsapp', $pejabatHp)
                        ->notify(new PermohonanCreatedWhatsapp($permohonan, $namaPejabat));
                }
            } catch (\Exception $e) {
                Log::error('WA Admin Notification failed: ' . $e->getMessage());
            }
            DB::commit();

            // Dispatch OCR verification job (queue) — tidak blocking user
            if ($service->ocr_rules && !empty($service->ocr_rules['dokumen'])) {
                VerifyPermohonanDocuments::dispatch($permohonan->id);
            }

            return redirect()->route('layanan.index')->with('success_application', [
                'token' => $trackToken,
                'message' => 'Permohonan berhasil diajukan! Simpan kode ini untuk cek status.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan permohonan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan permohonan. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Handle file uploads for permohonan documents.
     */
    private function handleFileUploads(StorePermohonanRequest $request, PermohonanSurat $permohonan): void
    {
        $fileFields = StorePermohonanRequest::fileFields();

        $labels = PermohonanDokumen::JENIS_DOKUMEN;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                // Disk privat (local) — berisi PII (KTP/KK/buku nikah). Tidak boleh
                // web-accessible; hanya disajikan lewat controller yang di-scope.
                $path = $file->store(
                    'permohonan/' . $permohonan->id . '/dokumen',
                    'local'
                );

                PermohonanDokumen::create([
                    'permohonan_surat_id' => $permohonan->id,
                    'nama_dokumen' => $labels[$field] ?? ucwords(str_replace('_', ' ', $field)),
                    'jenis_dokumen' => $field,
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }
    }

    /**
     * Show the revision form for a rejected permohonan.
     * Accessible publicly via track_token.
     */
    public function edit(string $trackToken)
    {
        $permohonan = PermohonanSurat::with(['jenisSurat', 'kelurahan', 'dokumens'])
            ->where('track_token', $trackToken)
            ->firstOrFail();

        if ($permohonan->status !== 'rejected') {
            return redirect()
                ->route('layanan.surat.tracking.search', ['track_token' => $trackToken])
                ->with('error', 'Hanya permohonan yang ditolak yang dapat direvisi.');
        }

        $pekerjaanList = \App\Models\Pekerjaan::orderBy('nama')->pluck('nama')->toArray();

        return view('user.permohonan.revisi', compact('permohonan', 'pekerjaanList'));
    }

    /**
     * Process the revision of a rejected permohonan.
     * Re-uses the same StorePermohonanRequest validation but files are optional on revision.
     */
    public function update(Request $request, string $trackToken)
    {
        $permohonan = PermohonanSurat::with(['jenisSurat', 'kelurahan'])
            ->where('track_token', $trackToken)
            ->firstOrFail();

        if ($permohonan->status !== 'rejected') {
            return redirect()
                ->route('layanan.surat.tracking.search', ['track_token' => $trackToken])
                ->with('error', 'Permohonan ini tidak bisa direvisi.');
        }

        try {
            $service = app(PermohonanSuratService::class);
            $jenisSurat = $permohonan->jenisSurat;

            // Build data_permohonan from request (same logic as store, excluding file/system fields)
            $excludeFields = array_merge(
                ['_token', '_method', 'jenis_surat_id', 'kelurahan_id'],
                StorePermohonanRequest::fileFields()
            );
            $rawData = $request->except($excludeFields);

            // Sanitize numeric fields (remove thousands separator dots)
            if (isset($rawData['jumlah_penghasilan'])) {
                $rawData['jumlah_penghasilan'] = str_replace('.', '', $rawData['jumlah_penghasilan']);
            }

            $dataPermohonan = array_map(fn($v) => is_string($v) ? strtoupper($v) : $v, $rawData);

            // Resolve pemohon fields based on jenis surat kode
            $namaPemohon = $alamatPemohon = $nikPemohon = $phonePemohon = '';
            switch (strtoupper($jenisSurat->kode)) {
                case 'SKM':
                    $namaPemohon  = $request->nama_pelapor ?? $permohonan->nama_pemohon;
                    $nikPemohon   = $request->nik_pelapor  ?? $permohonan->nik_pemohon;
                    $phonePemohon = $request->no_wa         ?? $permohonan->phone_pemohon;
                    $alamatPemohon = $request->alamat_pelapor ?? $permohonan->alamat_pemohon;
                    break;
                default:
                    $namaPemohon  = $request->nama_lengkap     ?? $permohonan->nama_pemohon;
                    $nikPemohon   = $request->nik_bersangkutan ?? $permohonan->nik_pemohon;
                    $phonePemohon = $request->no_wa             ?? $permohonan->phone_pemohon;
                    $alamatPemohon = $request->alamat_lengkap  ?? $permohonan->alamat_pemohon;
                    break;
            }

            $service->revisiPermohonan(
                $permohonan,
                [
                    'nama_pemohon'    => strtoupper($namaPemohon),
                    'nik_pemohon'     => $nikPemohon,
                    'phone_pemohon'   => $phonePemohon,
                    'alamat_pemohon'  => strtoupper($alamatPemohon),
                    'data_permohonan' => $dataPermohonan,
                ],
                function ($permohonan) use ($request) {
                    // Re-use existing handleFileUploads logic but with plain Request
                    $this->handleRevisiFileUploads($request, $permohonan);
                }
            );

            return redirect()
                ->route('home')
                ->with('success_application', [
                    'token'   => $trackToken,
                    'message' => 'Revisi permohonan berhasil diajukan ulang! Gunakan kode ini untuk memantau status.',
                ]);
        } catch (\Exception $e) {
            Log::error('Revisi permohonan gagal: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengajukan revisi. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Handle file uploads for revision (files are optional — only replace if new file uploaded).
     * If no new file uploaded, existing dokumen records are preserved.
     */
    private function handleRevisiFileUploads(Request $request, PermohonanSurat $permohonan): void
    {
        $fileFields = StorePermohonanRequest::fileFields();

        $labels = PermohonanDokumen::JENIS_DOKUMEN;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                // Delete existing dokumen record for this field type (and its file) if any
                $existing = $permohonan->dokumens()->where('jenis_dokumen', $field)->first();
                if ($existing) {
                    Storage::disk('local')->delete($existing->file_path);
                    $existing->delete();
                }

                // Disk privat (local) — dokumen PII.
                $path = $file->store('permohonan/' . $permohonan->id . '/dokumen', 'local');

                PermohonanDokumen::create([
                    'permohonan_surat_id' => $permohonan->id,
                    'nama_dokumen'  => $labels[$field] ?? ucwords(str_replace('_', ' ', $field)),
                    'jenis_dokumen' => $field,
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                ]);
            }
        }
    }

    /**
     * Handle OCR KTP Request using AI Provider.
     */
    public function ocrKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            $file = $request->file('ktp_image');
            $ktpData = $this->ktpOcr->extract($file);

            return response()->json([
                'success' => true,
                'message' => 'OCR berhasil.',
                'data'    => $ktpData,
            ]);
        } catch (AiProviderException $e) {
            Log::error('OCR KTP Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR. Silakan coba lagi atau isi data secara manual.',
            ], 500);
        }
    }
}
