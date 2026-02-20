<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Http\Requests\StorePermohonanRequest;
use App\Models\PermohonanSurat;
use App\Models\PermohonanApproval;
use App\Models\ApprovalFlow;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\PermohonanBaruNotification;
use App\Notifications\PermohonanCreatedWhatsapp;
use Illuminate\Support\Facades\Log;

class PublicPermohonanController extends Controller
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

        return view('user.permohonan.create_public', compact('service', 'kelurahan'));
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

            // Filter out non-data fields for JSON storage
            $mainFields = ['_token', 'jenis_surat_id', 'kelurahan_id', 'pemohon_nama', 'pemohon_nik', 'pemohon_phone', 'pemohon_alamat'];
            $dataPermohonan = $request->except($mainFields);

            // Generate Nomor Permohonan: REG/YYYYMMDD/RANDOM
            $nomorPermohonan = 'REG/' . date('Ymd') . '/' . strtoupper(Str::random(5));

            // Create Permohonan
            $permohonan = PermohonanSurat::create([
                'track_token' => $trackToken,
                'nomor_permohonan' => $nomorPermohonan,
                'jenis_surat_id' => $request->jenis_surat_id,
                'kelurahan_id' => $request->kelurahan_id,
                'created_by_user_id' => auth()->id(), // Nullable now in migration
                'nama_pemohon' => $request->pemohon_nama,
                'nik_pemohon' => $request->pemohon_nik,
                'phone_pemohon' => $request->pemohon_phone,
                'alamat_pemohon' => $request->pemohon_alamat,
                'data_permohonan' => $dataPermohonan, // Casted to array/json in model
                'keperluan' => 'Permohonan ' . $service->nama,
                'status' => 'pending',
                'current_step' => 1,
            ]);

            // Trigger Approval Flow
            // Logic: Always start with Staf Kelurahan (Verifikasi)
            // Determine Role Name (assuming seeded roles)
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
            // Using the Permohonan model itself as the Notifiable entity for ad-hoc notification
            // OR finding the user if logged in. For public guest, we rely on phone_pemohon.
            try {
                $permohonan->notify(new PermohonanCreatedWhatsapp($permohonan));
            } catch (\Exception $e) {
                // Log error but don't fail the transaction
                Log::error('WA Notification failed: ' . $e->getMessage());
            }
            DB::commit();

            return redirect()->route('home')->with('success_application', [
                'token' => $trackToken,
                'message' => 'Permohonan berhasil diajukan! Simpan kode ini untuk cek status.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Handle OCR KTP Request.
     */
    public function ocrKtp(Request $request)
    {
        $request->validate([
            'ktp_image' => 'required|image|max:2048', // Max 2MB
        ]);

        // Placeholder for OCR Logic
        // In the future, process the image $request->file('ktp_image') and extract data

        return response()->json([
            'success' => true,
            'message' => 'OCR processing placeholder. Logic to be implemented.',
            'data' => [
                'nik' => null,
                'nama' => null,
                'alamat' => null,
                // Add other fields as needed
            ]
        ]);
    }
}
