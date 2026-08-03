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
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
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
                    [
                        'jenis_dokumen' => 'skp_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
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
                    [
                        'jenis_dokumen' => 'skbm_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
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
                    [
                        'jenis_dokumen' => 'skm_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
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
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skjd_akta_kematian_perceraian',
                        'label' => 'Akta Kematian atau Surat Keterangan Kematian / Akta Perceraian',
                        'wajib' => true,
                        'instruksi' => 'Baca akta kematian/surat keterangan kematian atau akta perceraian pasangan sebelumnya. Cocokkan nama pemohon dengan inputan nama_lengkap. Dokumen ini membuktikan status janda/duda pemohon sesuai status_perkawinan.',
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
                    [
                        'jenis_dokumen' => 'sksi_ktp_kk_bersangkutan',
                        'label' => 'KTP & KK Pelapor',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK pelapor. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap, pekerjaan. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'sksi_ktp_saksi',
                        'label' => 'KTP 2 Orang Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'sksi_bukti_lunas_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'sksi_surat_pernyataan_penikah',
                        'label' => 'Surat Pernyataan Dari yang Menikahkan',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi surat pernyataan dari yang menikahkan beserta fotokopi KTP yang menikahkan dan diketahui 2 orang saksi.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pribadi pelapor (nama_lengkap, nik_bersangkutan) dan data pasangan (istri_nama, istri_nik) di semua dokumen. Verifikasi hubungan suami istri yang terkonfirmasi.',
            ],
            'SDNH' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'sdnh_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cocokkan nama dan NIK pemohon dengan inputan: nama_lengkap, nik_bersangkutan.',
                    ],
                    [
                        'jenis_dokumen' => 'sdnh_ktp_kk',
                        'label' => 'KTP dan KK Bersangkutan',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK pemohon (1 file gabungan). Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, status_perkawinan, pekerjaan, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'sdnh_formulir_n',
                        'label' => 'Formulir Pengantar Nikah (N1-N5) dari Kelurahan',
                        'wajib' => true,
                        'instruksi' => 'Baca formulir N1-N5 dari kelurahan. Cocokkan nama dan NIK pemohon dan pasangan dengan inputan nama_lengkap, nik_bersangkutan, nama_pasangan.',
                    ],
                    [
                        'jenis_dokumen' => 'sdnh_lunas_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'sdnh_rekom_kua',
                        'label' => 'Surat Rekomendasi KUA',
                        'wajib' => true,
                        'instruksi' => 'Baca surat rekomendasi KUA. Cocokkan nama pemohon dan pasangan dengan inputan nama_lengkap dan nama_pasangan, serta tanggal pernikahan dengan tanggal_pernikahan jika tertera.',
                    ],
                    [
                        'jenis_dokumen' => 'sdnh_akta_cerai_mati',
                        'label' => 'Fotokopi Akta Cerai atau Kematian (Jika Janda/Duda)',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi akta cerai atau akta kematian untuk membuktikan status belum menikah dari pernikahan sebelumnya, cocokkan nama dengan nama_lengkap.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pemohon (nama_lengkap, nik_bersangkutan) dan data pasangan (nama_pasangan) di semua dokumen. Pastikan detail pernikahan (tanggal_pernikahan, alamat_pernikahan) konsisten antara formulir N1-N5 dan rekomendasi KUA.',
            ],
            'SKG' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skg_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pelapor, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan RT/RW jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skg_blangko_pernyataan',
                        'label' => 'Surat Pernyataan Bermeterai 10.000 (Diketahui RT/RW)',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pernyataan bermeterai. Verifikasi pernyataan pelapor mengenai keberadaan/status orang yang dinyatakan gaib, cocokkan nama pelapor dengan nama_lengkap.',
                    ],
                    [
                        'jenis_dokumen' => 'skg_ktp_kk_bersangkutan',
                        'label' => 'KTP & KK Pelapor',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK PELAPOR (bukan orang yang gaib). Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap, pekerjaan. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skg_ktp_saksi',
                        'label' => 'KTP 2 Orang Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'skg_bukti_lunas_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skg_buku_nikah',
                        'label' => 'Buku Nikah / Surat Keterangan Nikah (Nikah Siri)',
                        'wajib' => true,
                        'instruksi' => 'Baca buku nikah atau surat keterangan nikah siri. Cocokkan nama pelapor (nama_lengkap) dan, jika tertera, nama pasangan/orang gaib (gaib_nama).',
                    ],
                ],
                'instruksi_global' => 'Cross-check data pelapor (nama_lengkap, nik_bersangkutan) konsisten di surat pengantar, blangko pernyataan, KTP/KK, dan buku nikah. Jika tersedia, cocokkan juga data orang gaib (gaib_nama, gaib_nik, gaib_tempat_lahir, gaib_tanggal_lahir) dengan buku nikah.',
            ],
            'SPN' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skmh_surat_pengantar',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cocokkan nama dan NIK pemohon dengan inputan: nama_lengkap, nik_bersangkutan.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_akta_ijazah_catin',
                        'label' => 'Akta Kelahiran & Ijazah Terakhir Kedua Calon Pengantin',
                        'wajib' => true,
                        'instruksi' => 'Baca akta kelahiran dan ijazah terakhir kedua calon pengantin (1 file gabungan). Cocokkan nama pemohon dengan inputan nama_lengkap dan tempat/tanggal lahir dengan tempat_lahir, tanggal_lahir.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_ktp_kk_catin',
                        'label' => 'KTP & KK Kedua Calon Pengantin',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK kedua calon pengantin (1 file gabungan). Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_ktp_kk_ortu',
                        'label' => 'KTP & KK Orang Tua/Wali Kedua Calon Pengantin',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK orang tua/wali (1 file gabungan). Cocokkan nama dan NIK dengan inputan ayah_nama, ayah_nik, ibu_nama, ibu_nik jika tercantum.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_pas_foto',
                        'label' => 'Pas Foto Warna Gandeng Pasangan (Latar Biru)',
                        'wajib' => true,
                        'instruksi' => 'Pastikan foto berwarna, menampilkan pasangan (2 orang) berdampingan dengan latar belakang biru.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_ktp_saksi',
                        'label' => 'KTP 2 Orang Saksi (RT yang sama, bukan saksi nikah)',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_form_n2_n5',
                        'label' => 'Formulir Pengantar Nikah (N2-N5)',
                        'wajib' => true,
                        'instruksi' => 'Baca formulir N2-N5. Cocokkan nama dan NIK pemohon dengan inputan nama_lengkap, nik_bersangkutan jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_akta_cerai_kematian',
                        'label' => 'Akta Cerai Hidup / Akta Kematian Pasangan Sebelumnya (jika Duda/Janda)',
                        'wajib' => false,
                        'instruksi' => 'Jika inputan status_perkawinan menunjukkan duda/janda dan dokumen ini diupload, verifikasi akta cerai atau akta kematian pasangan sebelumnya cocok dengan nama_lengkap.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_dispensasi_pengadilan',
                        'label' => 'Surat Dispensasi Kawin dari Pengadilan (jika belum usia 19 tahun)',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi surat dispensasi kawin dari pengadilan, cocokkan nama dengan nama_lengkap. Relevan jika usia dari tanggal_lahir belum mencapai 19 tahun.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_izin_atasan',
                        'label' => 'Surat Izin dari Atasan / Kesatuan (jika anggota TNI/POLRI)',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi surat izin dari atasan/kesatuan, relevan jika inputan pekerjaan menunjukkan TNI/POLRI.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_izin_poligami',
                        'label' => 'Penetapan Izin Poligami dari Pengadilan (jika beristri lebih dari seorang)',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi penetapan izin poligami dari pengadilan, cocokkan nama pemohon dengan nama_lengkap.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_rekom_dp3a',
                        'label' => 'Surat Rekomendasi dari DP3APMP2KB (jika di bawah usia menikah)',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, verifikasi surat rekomendasi dari DP3APMP2KB, relevan jika usia dari tanggal_lahir di bawah usia minimal menikah.',
                    ],
                    [
                        'jenis_dokumen' => 'skmh_surat_imunisasi_catin',
                        'label' => 'Surat Imunisasi Catin',
                        'wajib' => false,
                        'instruksi' => 'Jika dokumen ini diupload, pastikan surat imunisasi calon pengantin ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pemohon (nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir) di surat pengantar, KTP/KK, dan formulir N2-N5. Cocokkan data ayah (ayah_nama, ayah_nik) dan ibu (ibu_nama, ibu_nik) dengan KTP/KK orang tua/wali. Untuk dokumen tambahan (akta cerai/kematian, dispensasi, izin atasan, izin poligami, rekomendasi DP3A), pastikan relevan dengan status_perkawinan, usia, atau pekerjaan pemohon.',
            ],
            'SPKH' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'spkh_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'spkh_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap, status_perkawinan, pekerjaan. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'spkh_surat_pernyataan',
                        'label' => 'Surat Pernyataan Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pernyataan pemohon terkait kehilangan. Cocokkan dengan inputan: barang_hilang, tanggal_kehilangan, lokasi_kehilangan jika tertera di surat.',
                    ],
                    [
                        'jenis_dokumen' => 'spkh_ktp_saksi',
                        'label' => 'KTP 2 Orang Saksi',
                        'wajib' => true,
                        'instruksi' => 'Periksa apakah ada 2 KTP saksi yang BERBEDA SATU SAMA LAIN (nama dan NIK berbeda). JANGAN bandingkan dengan data pemohon. Fokus: verifikasi 2 identitas saksi BERBEDA dari satu sama lain.',
                    ],
                    [
                        'jenis_dokumen' => 'spkh_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pribadi pemohon (nama, NIK, alamat) di semua dokumen. Pastikan detail kehilangan (waktu, lokasi, barang hilang) konsisten antara surat pernyataan dan inputan form.',
            ],
            'SKB' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skb_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skb_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap, status_perkawinan, pekerjaan. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skb_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check konsistensi data pribadi pemohon (nama, NIK, alamat) di semua dokumen. Pastikan tujuan/keperluan dan tanggal berangkat pada inputan masuk akal (tidak di masa lalu).',
            ],
            'SKDKO' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skdko_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_sk_kepengurusan',
                        'label' => 'Struktur Organisasi / SK Kepengurusan',
                        'wajib' => true,
                        'instruksi' => 'Baca struktur organisasi/SK kepengurusan. Cocokkan nama kantor/organisasi dengan inputan nama_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_akta_pendirian',
                        'label' => 'File Akta Pendirian',
                        'wajib' => true,
                        'instruksi' => 'Baca akta pendirian organisasi/kantor. Cocokkan nama organisasi/kantor dan alamat dengan inputan nama_kantor dan alamat_jalan.',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_npwp',
                        'label' => 'File NPWP',
                        'wajib' => true,
                        'instruksi' => 'Baca NPWP. Cocokkan nama yang terdaftar dengan inputan nama_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'skdko_surat_pernyataan_warga',
                        'label' => 'Surat Pernyataan dari Lingkungan Sekitar (Minimal 20 Orang)',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pernyataan warga sekitar. Verifikasi bahwa surat menyatakan keberadaan kantor/organisasi di alamat yang cocok dengan inputan alamat_jalan.',
                    ],
                ],
                'instruksi_global' => 'Cross-check nama_kantor dan alamat_jalan konsisten di semua dokumen legalitas (SK kepengurusan, akta pendirian, NPWP, surat pernyataan warga).',
            ],
            'SPRIK' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'sprik_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'sprik_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'sprik_ktp_penyelenggara',
                        'label' => 'KTP Penyelenggara Kursus & Pelatihan/Pelatih/Pembimbing',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak nama dan NIK penyelenggara/pelatih/pembimbing kursus. Boleh berbeda dari data pemohon — JANGAN bandingkan dengan nama_lengkap/nik_bersangkutan, cukup pastikan KTP terbaca dan identitasnya valid.',
                    ],
                    [
                        'jenis_dokumen' => 'sprik_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check nama_lembaga dan alamat_tempat_kegiatan konsisten di semua dokumen. Pastikan data pemohon (nama, NIK) konsisten antara surat pengantar dan KTP/KK.',
            ],
            'SKDK' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'skdk_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_ktp_pemohon',
                        'label' => 'KTP Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_akta_kepengurusan',
                        'label' => 'Akta Pendirian / Kepengurusan',
                        'wajib' => true,
                        'instruksi' => 'Baca akta pendirian/kepengurusan. Cocokkan nama organisasi/partai dengan inputan nama_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_akta_parpol',
                        'label' => 'Akta Pendirian Partai Politik',
                        'wajib' => true,
                        'instruksi' => 'Baca akta pendirian partai politik. Cocokkan nama partai dengan inputan nama_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_imb_sewa',
                        'label' => 'IMB / Perjanjian Sewa Bangunan',
                        'wajib' => true,
                        'instruksi' => 'Baca IMB atau perjanjian sewa bangunan. Cocokkan alamat bangunan dengan inputan alamat_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_surat_pengantar_lurah',
                        'label' => 'Surat Pengantar Domisili Kepartaian dari Kelurahan',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar domisili kepartaian dari kelurahan. Cocokkan nama_kantor dan alamat_kantor dengan yang tertera.',
                    ],
                    [
                        'jenis_dokumen' => 'skdk_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check nama_kantor dan alamat_kantor konsisten di semua dokumen legalitas (akta kepengurusan, akta parpol, IMB/sewa, surat pengantar lurah).',
            ],
            'ROIPK' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'roipk_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_surat_permohonan',
                        'label' => 'Surat Permohonan Izin Penyelenggaraan Kursus',
                        'wajib' => true,
                        'instruksi' => 'Baca surat permohonan izin penyelenggaraan kursus. Cocokkan nama lembaga dengan inputan nama_lembaga.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_struktur_organisasi',
                        'label' => 'Struktur Organisasi',
                        'wajib' => true,
                        'instruksi' => 'Baca struktur organisasi lembaga kursus. Cocokkan nama lembaga dengan inputan nama_lembaga.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_ijazah_kompetensi',
                        'label' => 'Ijazah Kompetensi Penyelenggara dan Tenaga Pengajar',
                        'wajib' => true,
                        'instruksi' => 'Baca ijazah kompetensi penyelenggara dan tenaga pengajar. Pastikan dokumen ada dan terbaca, tidak perlu dibandingkan dengan data pemohon.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_izin_tetangga',
                        'label' => 'Izin Tetangga (Diketahui Ketua RT + Fotokopi KTP), khusus perumahan',
                        'wajib' => true,
                        'instruksi' => 'Baca surat izin tetangga yang diketahui Ketua RT, jika berlaku (khusus lokasi perumahan). Pastikan ada persetujuan tetangga dan diketahui RT.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_daftar_fasilitas',
                        'label' => 'Daftar Fasilitas Kelengkapan Belajar dan Warga Belajar',
                        'wajib' => true,
                        'instruksi' => 'Baca daftar fasilitas kelengkapan belajar dan daftar warga belajar. Pastikan dokumen ada dan terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_silabus',
                        'label' => 'Daftar Silabus Pembelajaran',
                        'wajib' => true,
                        'instruksi' => 'Baca daftar silabus pembelajaran. Cocokkan materi yang diajarkan dengan inputan materi_lembaga.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_surat_pengantar_lurah',
                        'label' => 'Surat Pengantar Rekomendasi Izin Penyelenggaraan Kursus dari Kelurahan',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar rekomendasi dari kelurahan. Cocokkan nama_lembaga dan alamat_lembaga dengan yang tertera.',
                    ],
                    [
                        'jenis_dokumen' => 'roipk_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check nama_lembaga dan alamat_lembaga konsisten di semua dokumen kelembagaan (surat permohonan, struktur organisasi, silabus, daftar fasilitas, surat pengantar lurah).',
            ],
            'SPKDK' => [
                'dokumen' => [
                    [
                        'jenis_dokumen' => 'spkdk_surat_pengantar_rtrw',
                        'label' => 'Surat Pengantar RT/RW Setempat',
                        'wajib' => true,
                        'instruksi' => 'Baca surat pengantar RT/RW. Cari nama dan NIK pemohon, bandingkan dengan inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat dan tanggal, cocokkan dengan no_surat_pengantar dan tanggal_surat_pengantar jika terbaca.',
                    ],
                    [
                        'jenis_dokumen' => 'spkdk_ktp_kk_pemohon',
                        'label' => 'KTP dan KK Pemohon',
                        'wajib' => true,
                        'instruksi' => 'Ekstrak data dari KTP dan KK. Bandingkan HANYA dengan inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat_lengkap. Tanggal lahir harus valid (tidak di masa depan dari hari ini).',
                    ],
                    [
                        'jenis_dokumen' => 'spkdk_struktur_organisasi',
                        'label' => 'Struktur Organisasi/SK Kepengurusan',
                        'wajib' => true,
                        'instruksi' => 'Baca struktur organisasi/SK kepengurusan. Cocokkan nama kantor/organisasi dengan inputan nama_kantor.',
                    ],
                    [
                        'jenis_dokumen' => 'spkdk_bukti_pbb',
                        'label' => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
                        'wajib' => true,
                        'instruksi' => 'Baca dokumen bukti lunas PBB tahun berjalan, pastikan dokumen ada dan terbaca.',
                    ],
                ],
                'instruksi_global' => 'Cross-check nama_kantor dan alamat_kantor konsisten di semua dokumen (struktur organisasi, surat pengantar).',
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
