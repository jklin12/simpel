<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuratCounter;
use App\Models\JenisSurat;

class SuratCounterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample Kelurahan ID: 6372010006 (Kelurahan Syamsudin Noor, asumsi)
        $kelurahanId = '6372010006';
        $currentYear = date('Y');
        $currentMonth = date('n');

        // Initialize counters for all letter types
        $jenisSurats = JenisSurat::all();

        foreach ($jenisSurats as $surat) {
            SuratCounter::updateOrCreate(
                [
                    'jenis_surat_id' => $surat->id,
                    'kelurahan_id' => $kelurahanId,
                    'tahun' => $currentYear,
                    'bulan' => $currentMonth,
                ],
                [
                    'counter' => 0 // Start from 0
                ]
            );
        }
    }
}
