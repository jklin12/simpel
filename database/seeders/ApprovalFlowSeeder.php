<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApprovalFlow;
use App\Models\ApprovalStep;
use App\Models\JenisSurat;
use Illuminate\Support\Facades\DB;

class ApprovalFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all Letter Types
        $jenisSurats = JenisSurat::all();

        // Get a default Kelurahan ID (e.g., Guntung Manggis from MasterLocationSeeder)
        $kelurahanId = 6372011004;

        foreach ($jenisSurats as $surat) {
            // Determine Flow Type based on Surat Code
            // Flow A (Kelurahan): Masyarakat -> Admin Kelurahan -> Selesai
            // Flow B (Kecamatan): Masyarakat -> Admin Kelurahan -> Admin Kecamatan -> Selesai

            $isKecamatanFlow = in_array($surat->kode, ['SKU', 'SKTM']);

            $flow = ApprovalFlow::updateOrCreate(
                [
                    'jenis_surat_id' => $surat->id,
                    'kelurahan_id' => $kelurahanId,
                ],
                [
                    'nama' => $isKecamatanFlow ? 'Flow Kecamatan' : 'Flow Kelurahan',
                    'require_kecamatan_approval' => $isKecamatanFlow,
                    'require_kabupaten_approval' => false,
                    'is_active' => true,
                ]
            );

            // Clear existing steps to avoid duplicates on re-seed
            ApprovalStep::where('approval_flow_id', $flow->id)->delete();

            // Step 1: Admin Kelurahan Verify (Common to both flows)
            ApprovalStep::create([
                'approval_flow_id' => $flow->id,
                'step_order' => 1,
                'role_name' => 'f_admin_kelurahan',
                'step_name' => 'Verifikasi Admin Kelurahan',
                'is_required' => true,
            ]);

            if ($isKecamatanFlow) {
                // Step 2: Admin Kecamatan Verify
                ApprovalStep::create([
                    'approval_flow_id' => $flow->id,
                    'step_order' => 2,
                    'role_name' => 'f_admin_kecamatan',
                    'step_name' => 'Verifikasi Admin Kecamatan',
                    'is_required' => true,
                ]);
            }
        }
    }
}
