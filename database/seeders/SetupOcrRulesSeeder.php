<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;

class SetupOcrRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ocrRulesData = [
            'SKTMR' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'sktmr_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama pemohon dan NIK, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan RT/RW jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'sktmr_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan Bermeterai',
                        'wajib' => true,
                        'instruksi' => 'Baca Blangko Pernyataan. Verifikasi data pemohon (nama, NIK, alamat) sesuai dengan inputan.',
                    ],
                    [
                        'jenis_dokumen' => 'sktmr_ktp_kk',
                        'label' => 'KTP & KK',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'sktmr_ktp_saksi',
                        'label' => 'KTP 2 Orang Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'sktmr_bukti_pbb',
                        'label' => 'Bukti Lunas PBB',
                        'wajib' => false,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan jika tersedia.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pribadi (nama, NIK, alamat) di semua dokumen. Pastikan tidak ada kontradiksi.',
            ],
            'SKP' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skp_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, ekstrak nama, NIK, RT/RW.',
                    ],
                    [
                        'jenis_dokumen' => 'skp_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi data pemohon dan informasi penghasilan jika ada di blangko pernyataan.',
                    ],
                    [
                        'jenis_dokumen' => 'skp_ktp_kk',
                        'label' => 'KTP & KK',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data identitas (nama, NIK, alamat) dari KTP dan KK. Bandingkan dengan inputan. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skp_ktp_saksi',
                        'label' => 'KTP Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                ],
                'instruksi_global' => 'Konsistensi data pribadi di semua dokumen.',
            ],
            'SKBM' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skbm_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, verifikasi nama dan NIK pemohon.',
                    ],
                    [
                        'jenis_dokumen' => 'skbm_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi data pemohon di blangko pernyataan.',
                    ],
                    [
                        'jenis_dokumen' => 'skbm_ktp_kk',
                        'label' => 'KTP & KK',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak dan verifikasi data identitas (nama, NIK, jenis kelamin, status perkawinan). Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skbm_ktp_saksi',
                        'label' => 'KTP 2 Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                ],
                'instruksi_global' => 'Cross-check data pribadi konsisten di semua dokumen.',
            ],
            'SKM' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skm_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, verifikasi informasi pemohon dan orang yang meninggal.',
                    ],
                    [
                        'jenis_dokumen' => 'skm_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi pernyataan kematian di blangko pernyataan.',
                    ],
                    [
                        'jenis_dokumen' => 'skm_ktp_kk_pemohon',
                        'label' => 'KTP & KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data identitas pemohon dari KTP dan KK. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skm_ktp_kk_meninggal',
                        'label' => 'KTP & KK Orang yang Meninggal',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data identitas orang yang meninggal dari KTP dan KK. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skm_ktp_saksi',
                        'label' => 'KTP Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                ],
                'instruksi_global' => 'Cross-check data pemohon dan orang meninggal di semua dokumen.',
            ],
            'SKJD' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skjd_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, verifikasi nama dan NIK.',
                    ],
                    [
                        'jenis_dokumen' => 'skjd_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi data status perkawinan (janda/duda) di blangko pernyataan.',
                    ],
                    [
                        'jenis_dokumen' => 'skjd_ktp_kk_bersangkutan',
                        'label' => 'KTP & KK',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data identitas dan verifikasi status perkawinan dari KTP dan KK. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skjd_ktp_saksi',
                        'label' => 'KTP Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'skjd_bukti_lunas_pbb',
                        'label' => 'Bukti Lunas PBB',
                        'wajib' => false,
                        'instruksi' => 'Verifikasi bukti lunas PBB tahun berjalan jika ada.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pribadi dan status perkawinan.',
            ],
            'SKSI' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'sksi_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, verifikasi nama dan NIK kedua pihak.',
                    ],
                    [
                        'jenis_dokumen' => 'sksi_blangko_pernyataan',
                        'label' => 'Blangko Pernyataan',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi pernyataan hubungan suami istri di blangko pernyataan.',
                    ],
                ],
                'instruksi_global' => 'Verifikasi hubungan suami istri yang terkonfirmasi.',
            ],
            'SDNH' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'sdnh_akta_cerai_mati',
                        'label' => 'Akta Cerai/Mati',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi akta cerai atau akta mati untuk membuktikan status belum menikah dari pernikahan sebelumnya.',
                    ],
                ],
                'instruksi_global' => 'Verifikasi status belum menikah dari pernikahan sebelumnya.',
            ],
            'SKG' => [
                'dokumen' => [],
                'instruksi_global' => 'Tidak ada dokumen upload untuk SKG.',
            ],
            'SPN' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skmh_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar, verifikasi data calon pengantin.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_akta_ijazah_catin',
                        'label' => 'Akta Lahir/Ijazah Calon Pengantin',
                        'wajib' => true,
                        'instruksi' => 'Verifikasi akta lahir atau ijazah calon pengantin.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_ktp_kk_catin',
                        'label' => 'KTP & KK Calon Pengantin',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak dan verifikasi data identitas calon pengantin. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                ],
                'instruksi_global' => 'Cross-check data calon pengantin di semua dokumen.',
            ],
            'SPKH' => [
                'dokumen' => [],
                'instruksi_global' => 'Dokumen disesuaikan dengan kasus kehilangan.',
            ],
            'SKB' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi alasan bepergian.',
            ],
            'SKDKO' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi data domisili kantor/organisasi.',
            ],
            'SPRIK' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi data rekomendasi operasional untuk izin kursus.',
            ],
            'SKDK' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi data domisili kepartaian.',
            ],
            'ROIPK' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi rekomendasi operasional izin penyelenggaraan kursus.',
            ],
            'SPKDK' => [
                'dokumen' => [],
                'instruksi_global' => 'Verifikasi surat pengantar dan keterangan domisili kepartaian.',
            ],
        ];

        foreach ($ocrRulesData as $kode => $rules) {
            $jenisSurat = JenisSurat::where('kode', $kode)->first();

            if ($jenisSurat) {
                // Always update to apply latest bug fixes and improvements
                $jenisSurat->update(['ocr_rules' => $rules]);
                echo "✅ OCR Rules updated for {$kode}\n";
            } else {
                echo "❌ {$kode} not found in database\n";
            }
        }

        echo "\n✅ OCR Rules setup completed!\n";
    }
}
