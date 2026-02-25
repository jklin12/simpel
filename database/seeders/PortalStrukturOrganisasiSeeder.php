<?php

namespace Database\Seeders;

use App\Models\PortalStrukturOrganisasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortalStrukturOrganisasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel sebelum disembai (opsional, tp berguna utk testing)
        DB::table('portal_struktur_organisasis')->truncate();

        // 1. Level 1 (Camat) - Tidak punya parent
        $camat = PortalStrukturOrganisasi::create([
            'nama'    => 'Bambang Supriyanto, S.STP., M.Si',
            'jabatan' => 'Camat Landasan Ulin',
            'urutan'  => 1,
        ]);

        // 2. Level 2 (Sekcam) - Bawahan langsung Camat
        $sekcam = PortalStrukturOrganisasi::create([
            'nama'      => 'Hj. Siti Aminah, S.Sos',
            'jabatan'   => 'Sekretaris Kecamatan',
            'parent_id' => $camat->id,
            'urutan'    => 1,
        ]);

        // 3. Level 3 (Kepala Seksi / Kepala Sub Bagian) - Bawahan Sekcam
        PortalStrukturOrganisasi::create([
            'nama'      => 'Ahmad Fauzi, SE',
            'jabatan'   => 'Kasi Tata Pemerintahan',
            'parent_id' => $sekcam->id,
            'urutan'    => 1,
        ]);

        PortalStrukturOrganisasi::create([
            'nama'      => 'Rina Astuti, S.AP',
            'jabatan'   => 'Kasi Pemberdayaan Masyarakat',
            'parent_id' => $sekcam->id,
            'urutan'    => 2,
        ]);

        PortalStrukturOrganisasi::create([
            'nama'      => 'Budi Santoso, S.Kom',
            'jabatan'   => 'Kasubag Perencanaan & Keuangan',
            'parent_id' => $sekcam->id,
            'urutan'    => 3,
        ]);

        PortalStrukturOrganisasi::create([
            'nama'      => 'Dewi Lestari, SH',
            'jabatan'   => 'Kasubag Umum & Kepegawaian',
            'parent_id' => $sekcam->id,
            'urutan'    => 4,
        ]);

        PortalStrukturOrganisasi::create([
            'nama'      => 'H. Agus Salim, S.Ag',
            'jabatan'   => 'Kasi Kesejahteraan Sosial',
            'parent_id' => $sekcam->id,
            'urutan'    => 5,
        ]);

        PortalStrukturOrganisasi::create([
            'nama'      => 'Joko Widodo, S.IP',
            'jabatan'   => 'Kasi Ketentraman dan Ketertiban',
            'parent_id' => $sekcam->id,
            'urutan'    => 6,
        ]);

        // 4. Level 4 (Staf kelurahan under Kasi Pemerintahan) - Contoh saja
        $kasiPem = PortalStrukturOrganisasi::where('jabatan', 'Kasi Tata Pemerintahan')->first();
        if ($kasiPem) {
            PortalStrukturOrganisasi::create([
                'nama'      => 'Aris Munandar',
                'jabatan'   => 'Staf Pemerintahan',
                'parent_id' => $kasiPem->id,
                'urutan'    => 1,
            ]);
        }
    }
}
