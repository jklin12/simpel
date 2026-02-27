<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PortalDataKelurahan;
use App\Models\Kelurahan;

class PortalDataKelurahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada setidaknya satu kelurahan di Landasan Ulin
        $kelurahan = Kelurahan::where('kecamatan_id', '6372010')->first();

        if (!$kelurahan) {
            $this->command->warn('Tidak ada data Kelurahan di Kecamatan Landasan Ulin. Seeder PortalDataKelurahan dilewati.');
            return;
        }

        $data = [
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'rw',
                'nama'         => 'Rukun Warga 01',
                'keterangan'   => 'Ketua RW: Bapak Budi Santoso. Mencakup wilayah RT 01 hingga RT 05.',
                'alamat'       => 'Jl. Golf Raya No. 10',
                'latitude'     => -3.4545,
                'longitude'    => 114.7702,
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'rt',
                'nama'         => 'Rukun Tetangga 01 / RW 01',
                'keterangan'   => 'Ketua RT: Bapak Slamet.',
                'alamat'       => 'Jl. Golf Raya Gg. Manggis No. 2',
                'latitude'     => -3.4550,
                'longitude'    => 114.7710,
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'tempat_ibadah',
                'nama'         => 'Masjid Jami Al-Hidayah',
                'keterangan'   => 'Masjid berasitektur modern, dapat menampung hingga 500 jamaah.',
                'alamat'       => 'Jl. Angkasa Permai No. 5',
                'latitude'     => -3.4490,
                'longitude'    => 114.7685,
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'sarana_pendidikan',
                'nama'         => 'SDN Landasan Ulin 1',
                'keterangan'   => 'Sekolah Dasar Negeri berakreditasi A.',
                'alamat'       => 'Jl. Kasturi Indah No. 12',
                'latitude'     => -3.4512,
                'longitude'    => 114.7725,
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'fasilitas_kesehatan',
                'nama'         => 'Puskesmas Landasan Ulin',
                'keterangan'   => 'Melayani rawat inap, IGD 24 jam, dan poli umum.',
                'alamat'       => 'Jl. Sukamara No. 8',
                'latitude'     => -3.4601,
                'longitude'    => 114.7800,
            ],
            [
                'kelurahan_id' => $kelurahan->id,
                'kategori'     => 'pos_kamling',
                'nama'         => 'Pos Kamling RT 03',
                'keterangan'   => 'Pos ronda aktif dengan jadwal jaga bergiliran.',
                'alamat'       => 'Jl. Kenari Ujung',
                'latitude'     => -3.4570,
                'longitude'    => 114.7650,
            ],
        ];

        foreach ($data as $item) {
            PortalDataKelurahan::create($item);
        }

        $this->command->info('Seeder Portal Data Kelurahan berhasil dijalankan!');
    }
}
