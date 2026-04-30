<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanLandasanUlinCoordinatesSeeder extends Seeder
{
    /**
     * Seed approximate centroid coordinates for Kecamatan Landasan Ulin
     * dan kelurahan-kelurahan di dalamnya.
     *
     * Koordinat = perkiraan titik tengah berdasarkan peta publik (OSM).
     * Boleh diperbarui via UI master kelurahan.
     */
    public function run(): void
    {
        DB::table('m_kecamatans')
            ->where('id', 6372010)
            ->update([
                'latitude'  => -3.4421,
                'longitude' => 114.7567,
            ]);

        // Pakai LIKE pattern agar fleksibel terhadap variasi penulisan di DB
        // (mis. "GUNTUNGMANGGIS" tanpa spasi vs "Guntung Manggis").
        $kelurahans = [
            '%Syamsudin%Noor%'        => ['lat' => -3.4368, 'lng' => 114.7534],
            '%Guntung%Payung%'        => ['lat' => -3.4342, 'lng' => 114.7664],
            '%Guntung%Manggis%'       => ['lat' => -3.4567, 'lng' => 114.7821],
            '%Guntungmanggis%'        => ['lat' => -3.4567, 'lng' => 114.7821],
            '%Landasan%Ulin%Timur%'   => ['lat' => -3.4528, 'lng' => 114.7395],
            '%Landasan%Ulin%Utara%'   => ['lat' => -3.4498, 'lng' => 114.7610],
            '%Landasan%Ulin%Tengah%'  => ['lat' => -3.4445, 'lng' => 114.7505],
            '%Landasan%Ulin%Barat%'   => ['lat' => -3.4410, 'lng' => 114.7290],
            '%Landasan%Ulin%Selatan%' => ['lat' => -3.4612, 'lng' => 114.7488],
        ];

        foreach ($kelurahans as $pattern => $coords) {
            DB::table('m_kelurahans')
                ->where('kecamatan_id', 6372010)
                ->where('nama', 'LIKE', $pattern)
                ->update([
                    'latitude'  => $coords['lat'],
                    'longitude' => $coords['lng'],
                ]);
        }

        $this->command->info('Koordinat Kecamatan Landasan Ulin & kelurahan ter-update.');
    }
}
