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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Notifications\PermohonanBaruNotification;
use App\Notifications\PermohonanCreatedWhatsapp;
use Illuminate\Support\Facades\Log;

class PermohonanController extends Controller
{
    /**
     * Show the application form.
     */
    public function create(Request $request)
    {
        $serviceId = $request->query('service_id');
        $kelurahanId = $request->query('kelurahan_id');

        if (!$serviceId || !$kelurahanId) {
            return redirect()->route('services.index')->with('error', 'Silakan pilih layanan dan lokasi terlebih dahulu.');
        }

        $service = JenisSurat::findOrFail($serviceId);
        $kelurahan = Kelurahan::findOrFail($kelurahanId);

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

            // Filter out non-data fields AND file fields for JSON storage
            $dynamicFileNames = collect($service->required_fields ?? [])
                ->filter(fn($f) => ($f['type'] ?? '') === 'file')
                ->pluck('name')
                ->toArray();
            $excludeFields = array_unique(array_merge(
                ['_token', 'jenis_surat_id', 'kelurahan_id'],
                StorePermohonanRequest::fileFields(),
                $dynamicFileNames
            ));
            $dataPermohonan = array_map(fn($v) => is_string($v) ? strtoupper($v) : $v, $request->except($excludeFields));

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
            $targetRole = 'admin_kelurahan';

            PermohonanApproval::create([
                'permohonan_surat_id' => $permohonan->id,
                'target_role' => $targetRole,
                'step_name' => 'Verifikasi Berkas',
                'step_order' => 1,
                'status' => 'pending',
            ]);

            // Notify Approvers
            $approvers = \App\Models\User::role($targetRole)
                ->where('kelurahan_id', $request->kelurahan_id)
                ->get();

            foreach ($approvers as $approver) {
                $approver->notify(new PermohonanBaruNotification($permohonan));
            }

            // Notify Applicant (WhatsApp)
            try {
                $permohonan->notify(new PermohonanCreatedWhatsapp($permohonan));
            } catch (\Exception $e) {
                // Log error but don't fail the transaction
                Log::error('WA Notification failed: ' . $e->getMessage());
            }
            DB::commit();

            return redirect()->route('layanan.index')->with('success_application', [
                'token' => $trackToken,
                'message' => 'Permohonan berhasil diajukan! Simpan kode ini untuk cek status.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle file uploads for permohonan documents.
     */
    private function handleFileUploads(StorePermohonanRequest $request, PermohonanSurat $permohonan): void
    {
        $fileFields = StorePermohonanRequest::fileFields();

        // Merge dynamic file fields from required_fields
        $jenisSurat = $permohonan->jenisSurat;
        $dynamicFileFields = collect($jenisSurat->required_fields ?? [])
            ->filter(fn($f) => ($f['type'] ?? '') === 'file')
            ->pluck('name')
            ->toArray();
        $fileFields = array_unique(array_merge($fileFields, $dynamicFileFields));

        $labels = PermohonanDokumen::JENIS_DOKUMEN;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = $file->store(
                    'permohonan/' . $permohonan->id . '/dokumen',
                    'public'
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
            $dynamicFileNames = collect($jenisSurat->required_fields ?? [])
                ->filter(fn($f) => ($f['type'] ?? '') === 'file')
                ->pluck('name')
                ->toArray();
            $excludeFields = array_unique(array_merge(
                ['_token', '_method', 'jenis_surat_id', 'kelurahan_id'],
                StorePermohonanRequest::fileFields(),
                $dynamicFileNames
            ));
            $rawData = $request->except($excludeFields);
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
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle file uploads for revision (files are optional — only replace if new file uploaded).
     * If no new file uploaded, existing dokumen records are preserved.
     */
    private function handleRevisiFileUploads(Request $request, PermohonanSurat $permohonan): void
    {
        $jenisSurat = $permohonan->jenisSurat;
        $fileFields = StorePermohonanRequest::fileFields();
        $dynamicFileFields = collect($jenisSurat->required_fields ?? [])
            ->filter(fn($f) => ($f['type'] ?? '') === 'file')
            ->pluck('name')
            ->toArray();
        $fileFields = array_unique(array_merge($fileFields, $dynamicFileFields));

        $labels = PermohonanDokumen::JENIS_DOKUMEN;

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                // Delete existing dokumen record for this field type (and its file) if any
                $existing = $permohonan->dokumens()->where('jenis_dokumen', $field)->first();
                if ($existing) {
                    Storage::disk('public')->delete($existing->file_path);
                    $existing->delete();
                }

                $path = $file->store('permohonan/' . $permohonan->id . '/dokumen', 'public');

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
     * Handle OCR KTP Request using Claude AI Vision API.
     */
    public function ocrKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|max:5120', // Max 5MB
        ]);

        // Mock data for local development to save Claude API tokens
        if (app()->environment('local')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'nik' => '637201' . rand(1000000000, 9999999999), // Random 16 digits
                    'nama' => 'MOCK ' . \Illuminate\Support\Str::random(10),
                    'tempat_lahir' => 'BANJARBARU',
                    'tanggal_lahir' => '1995-05-15',
                    'jenis_kelamin' => rand(0, 1) ? 'Laki-laki' : 'Perempuan',
                    'alamat' => 'JL. RAYA PUSAKA NO. 123, RT 01 RW 02, KEL. GUNTUNG P., KEC. BANJARBARU',
                    'agama' => 'ISLAM',
                    'status_perkawinan' => 'BELUM KAWIN',
                    'pekerjaan' => 'KARYAWAN SWASTA'
                ]
            ]);
        }

        $apiKey = config('services.claude.api_key');
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Claude API key tidak dikonfigurasi.',
            ], 500);
        }

        try {
            $file = $request->file('ktp_image');
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mimeType  = $file->getMimeType();

            $prompt = <<<EOT
Kamu adalah sistem OCR KTP Indonesia. Ekstrak data dari foto KTP ini dan kembalikan HANYA JSON valid (tanpa komentar, tanpa markdown) dengan format berikut:
{
  "nik": "string 16 digit atau null",
  "nama": "string nama lengkap atau null",
  "tempat_lahir": "string nama kota atau null",
  "tanggal_lahir": "string format YYYY-MM-DD atau null",
  "jenis_kelamin": "Laki-laki atau Perempuan atau null",
  "alamat": "string alamat lengkap atau null",
  "agama": "string agama atau null",
  "status_perkawinan": "Belum Kawin / Kawin / Cerai Hidup / Cerai Mati atau null",
  "pekerjaan": "string pekerjaan atau null"
}

Aturan penting:
- tanggal_lahir harus dalam format YYYY-MM-DD (misal: 1995-07-25)
- Untuk jenis_kelamin, agama, status_perkawinan, dan pekerjaan usahakan tebak dengan sebaik mungkin jika agak buram.
- Jika ada field yang sama sekali tidak terbaca, isi null
- Kembalikan HANYA JSON, jangan ada teks lain sama sekali
EOT;

            $payload = [
                'model'      => 'claude-3-haiku-20240307',
                'max_tokens' => 512,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            [
                                'type'   => 'image',
                                'source' => [
                                    'type'       => 'base64',
                                    'media_type' => $mimeType,
                                    'data'       => $imageData,
                                ],
                            ],
                            [
                                'type' => 'text',
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
            ];

            $ch = curl_init('https://api.anthropic.com/v1/messages');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => [
                    'x-api-key: ' . $apiKey,
                    'anthropic-version: 2023-06-01',
                    'content-type: application/json',
                ],
                CURLOPT_TIMEOUT => 30,
            ]);

            $response   = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError  = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('cURL error: ' . $curlError);
            }

            $responseData = json_decode($response, true);

            if ($httpStatus !== 200) {
                $errorMsg = $responseData['error']['message'] ?? 'Kesalahan API Claude.';
                throw new \Exception($errorMsg);
            }

            $rawText = $responseData['content'][0]['text'] ?? '';

            // Bersihkan markdown block jika ada
            $rawText = preg_replace('/```json\s*|\s*```/', '', trim($rawText));

            $ktpData = json_decode($rawText, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($ktpData)) {
                throw new \Exception('Gagal mengurai respon OCR dari Claude.');
            }

            return response()->json([
                'success' => true,
                'message' => 'OCR berhasil.',
                'data'    => [
                    'nik'               => $ktpData['nik'] ?? null,
                    'nama'              => $ktpData['nama'] ?? null,
                    'tempat_lahir'      => $ktpData['tempat_lahir'] ?? null,
                    'tanggal_lahir'     => $ktpData['tanggal_lahir'] ?? null,
                    'jenis_kelamin'     => $ktpData['jenis_kelamin'] ?? null,
                    'alamat'            => $ktpData['alamat'] ?? null,
                    'agama'             => $ktpData['agama'] ?? null,
                    'status_perkawinan' => $ktpData['status_perkawinan'] ?? null,
                    'pekerjaan'         => $ktpData['pekerjaan'] ?? null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('OCR KTP Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses OCR: ' . $e->getMessage(),
            ], 500);
        }
    }
}
