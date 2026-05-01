<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use App\Models\Kelurahan;
use App\Models\PermohonanSurat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TestDashboardDataSeeder extends Seeder
{
    /**
     * Seed test data untuk dashboard executive testing.
     * Buat permohonan surat dummy untuk setiap kelurahan Landasan Ulin.
     */
    public function run(): void
    {
        // Ambil kelurahan di Landasan Ulin (kecamatan_id = 6372010)
        $kelurahans = Kelurahan::where('kecamatan_id', 6372010)->get();

        if ($kelurahans->isEmpty()) {
            $this->command->warn('Tidak ada kelurahan di kecamatan 6372010');
            return;
        }

        $jenisSurat = JenisSurat::where('is_active', true)->first();
        if (!$jenisSurat) {
            $this->command->warn('Tidak ada jenis surat aktif');
            return;
        }

        $now = Carbon::now();
        $startMonth = $now->copy()->startOfMonth();

        // Seed 50-200 permohonan acak untuk setiap kelurahan selama bulan ini
        $kelurahans->each(function ($kel) use ($jenisSurat, $startMonth, $now) {
            $count = rand(5, 20);
            for ($i = 0; $i < $count; $i++) {
                $createdAt = $startMonth->copy()->addDays(rand(0, 28))->addHours(rand(8, 17))->addMinutes(rand(0, 59));

                PermohonanSurat::create([
                    'nomor_permohonan' => 'REG/' . $createdAt->format('Ymd') . '/' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'track_token' => strtoupper(substr(md5(uniqid()), 0, 10)),
                    'nama_pemohon' => 'Pemohon ' . fake()->lastName(),
                    'nik_pemohon' => str_pad(rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT),
                    'jenis_surat_id' => $jenisSurat->id,
                    'kelurahan_id' => $kel->id,
                    'status' => $this->randomStatus(),
                    'data_permohonan' => [],
                    'created_at' => $createdAt,
                    'completed_at' => rand(0, 1) ? $createdAt->copy()->addHours(rand(1, 72)) : null,
                ]);
            }
        });

        $this->command->info('Test dashboard data seeded successfully!');
    }

    private function randomStatus(): string
    {
        $rand = rand(0, 100);
        if ($rand < 30) return 'pending';
        if ($rand < 60) return 'approved';
        if ($rand < 90) return 'completed';
        return 'rejected';
    }
}
