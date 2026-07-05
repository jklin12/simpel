<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\JenisSurat;

class StorePermohonanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
        $commonRules = [
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'kelurahan_id' => 'required|exists:m_kelurahans,id',

            // removed pemohon_nama, pemohon_nik, pemohon_phone, pemohon_alamat
            // because they will be mapped in the controller dynamically
        ];

        // Fetch Type based on ID
        $jenisSurat = JenisSurat::find($this->jenis_surat_id);

        if (!$jenisSurat) return $commonRules;

        $specificRules = [];

        // Switch Logic based on kode
        // Ensure your DB 'kode' matches nicely (e.g., SKM, SKU)
        switch (strtoupper($jenisSurat->kode)) {
            case 'SKM': // Surat Keterangan Kematian
                $specificRules = $this->getSkmRules();
                break;
            case 'SKTM': // Surat Keterangan Tidak Mampu
                $specificRules = $this->getSktmRules();
                break;
            case 'SKTMR': // Surat Keterangan Belum Memiliki Rumah
                $specificRules = $this->getSktmrRules();
                break;
            case 'SKBM': // Surat Keterangan Belum Menikah
                $specificRules = $this->getSkbmRules();
                break;
            case 'SPN': // Surat Keterangan Menikah
                $specificRules = $this->getSkmhRules();
                break;
            case 'SKP': // Surat Keterangan Penghasilan
                $specificRules = $this->getSkpRules();
                break;
            case 'SKJD': // Surat Keterangan Janda/Duda
                $specificRules = $this->getSkjdRules();
                break;
            case 'SKSI': // Surat Keterangan Suami Istri
                $specificRules = $this->getSksiRules();
                break;
            case 'SKG': // Surat Keterangan Gaib
                $specificRules = $this->getSkgRules();
                break;
            case 'SKU': // Surat Keterangan Usaha (Example)
                // $specificRules = $this->getSkuRules();
                break;
            case 'SDNH': // Surat Dispensasi Nikah
                $specificRules = $this->getSdnhRules();
                break;
            case 'SPKH': // Surat Pengantar Keterangan Kehilangan
                $specificRules = $this->getSpkhRules();
                break;
            case 'SKB': // Surat Keterangan Bepergian
                $specificRules = $this->getSkbRules();
                break;
            case 'SKDKO': // Surat Keterangan Domisili Kantor/Sekretariat/Organisasi
                $specificRules = $this->getSkdkoRules();
                break;
            case 'SPRIK': // Surat Pengantar Rekomendasi Operasional Izin Kursus
                $specificRules = $this->getSprikRules();
                break;
            case 'SKDK': // Surat Pengantar Domisili Kepartaian
                $specificRules = $this->getSkdkRules();
                break;
            case 'ROIPK': // Rekomendasi Operasional Izin Penyelenggaraan Kursus
                $specificRules = $this->getRoipkRules();
                break;
            case 'SPKDK': // Surat Pengantar Keterangan Domisili Kepartaian
                $specificRules = $this->getSpkdkRules();
                break;
            default:
                if ($jenisSurat->required_fields) {
                    foreach ($jenisSurat->required_fields as $field) {
                        $specificRules[$field['name']] = match ($field['type']) {
                            'file'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
                            'date'   => ($field['is_required'] ? 'required' : 'nullable') . '|date',
                            'number' => ($field['is_required'] ? 'required' : 'nullable') . '|numeric',
                            default  => ($field['is_required'] ? 'required' : 'nullable') . '|string|max:1000',
                        };
                    }
                }
                break;
        }

        return array_merge($commonRules, $specificRules);
    }

    /**
     * File input names that should be excluded from data_permohonan JSON.
     */
    public static function fileFields(): array
    {
        return [
            // SKM
            'skm_surat_pengantar',
            'skm_blangko_pernyataan',
            'skm_ktp_kk_pemohon',
            'skm_ktp_kk_meninggal',
            'skm_ktp_saksi',
            'skm_bukti_pbb',
            // SKTM
            'surat_pengantar_rtrw',
            'blangko_pernyataan',
            'ktp_kk_bersangkutan',
            'ktp_saksi',
            'surat_rekomendasi_sekolah',
            'bukti_lunas_pbb',
            // SKTMR
            'sktmr_surat_pengantar',
            'sktmr_blangko_pernyataan',
            'sktmr_ktp_kk',
            'sktmr_ktp_saksi',
            'sktmr_bukti_pbb',
            // SKBM
            'skbm_surat_pengantar',
            'skbm_blangko_pernyataan',
            'skbm_ktp_kk',
            'skbm_ktp_saksi',
            'skbm_bukti_pbb',
            // SKP
            'skp_surat_pengantar',
            'skp_blangko_pernyataan',
            'skp_ktp_kk',
            'skp_ktp_saksi',
            'skp_bukti_pbb',
            // SPN
            'skmh_surat_pengantar',
            'skmh_akta_ijazah_catin',
            'skmh_ktp_kk_catin',
            'skmh_ktp_kk_ortu',
            'skmh_pas_foto',
            'skmh_ktp_saksi',
            'skmh_form_n2_n5',
            'skmh_akta_cerai_kematian',
            'skmh_dispensasi_pengadilan',
            'skmh_izin_atasan',
            'skmh_izin_poligami',
            'skmh_rekom_dp3a',
            'skmh_surat_imunisasi_catin',
            'skmh_bukti_pbb',
            // SKJD
            'skjd_surat_pengantar_rtrw',
            'skjd_blangko_pernyataan',
            'skjd_ktp_kk_bersangkutan',
            'skjd_ktp_saksi',
            'skjd_bukti_lunas_pbb',
            'skjd_akta_kematian_perceraian',
            // SDNH
            'sdnh_surat_pengantar',
            'sdnh_ktp_kk',
            'sdnh_formulir_n',
            'sdnh_lunas_pbb',
            'sdnh_rekom_kua',
            'sdnh_akta_cerai_mati',
            // SKSI
            'sksi_surat_pengantar_rtrw',
            'sksi_blangko_pernyataan',
            'sksi_ktp_kk_bersangkutan',
            'sksi_ktp_saksi',
            'sksi_bukti_lunas_pbb',
            'sksi_surat_pernyataan_penikah',
            // SKG
            'skg_surat_pengantar_rtrw',
            'skg_blangko_pernyataan',
            'skg_ktp_kk_bersangkutan',
            'skg_ktp_saksi',
            'skg_bukti_lunas_pbb',
            'skg_buku_nikah',
            // SPKH
            'spkh_surat_pengantar_rtrw',
            'spkh_ktp_kk_pemohon',
            'spkh_surat_pernyataan',
            'spkh_ktp_saksi',
            'spkh_bukti_pbb',
            // SKB
            'skb_surat_pengantar_rtrw',
            'skb_ktp_kk_pemohon',
            'skb_bukti_pbb',
            // SKDKO
            'skdko_surat_pengantar_rtrw',
            'skdko_ktp_kk_pemohon',
            'skdko_sk_kepengurusan',
            'skdko_bukti_pbb',
            'skdko_akta_pendirian',
            'skdko_npwp',
            'skdko_surat_pernyataan_warga',
            // SPRIK
            'sprik_surat_pengantar_rtrw',
            'sprik_ktp_kk_pemohon',
            'sprik_ktp_penyelenggara',
            'sprik_bukti_pbb',
            // SKDK
            'skdk_surat_pengantar_rtrw',
            'skdk_ktp_pemohon',
            'skdk_akta_kepengurusan',
            'skdk_akta_parpol',
            'skdk_imb_sewa',
            'skdk_surat_pengantar_lurah',
            'skdk_bukti_pbb',
            // ROIPK
            'roipk_surat_pengantar_rtrw',
            'roipk_ktp_kk_pemohon',
            'roipk_surat_permohonan',
            'roipk_struktur_organisasi',
            'roipk_ijazah_kompetensi',
            'roipk_izin_tetangga',
            'roipk_daftar_fasilitas',
            'roipk_silabus',
            'roipk_surat_pengantar_lurah',
            'roipk_bukti_pbb',
            // SPKDK
            'spkdk_surat_pengantar_rtrw',
            'spkdk_ktp_kk_pemohon',
            'spkdk_struktur_organisasi',
            'spkdk_bukti_pbb',
            // Legacy / other
            'foto_ktp',
            'foto_kk',
            'surat_pengantar_rt',
            'foto_rumah',
            'foto_usaha',
            'akta_kelahiran',
            'surat_pernyataan',
            'surat_bidan',
            'surat_rs',
            'akta_pendirian',
            'dokumen_lainnya',
        ];
    }

    private function getSpkdkRules(): array
    {
        return [
            'nama_lengkap'               => 'required|string|max:255',
            'nik_bersangkutan'           => 'required|string|size:16',
            'no_wa'                      => 'required|string|max:20',
            'jenis_kelamin'              => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'               => 'required|string|max:100',
            'tanggal_lahir'              => 'required|date',
            'alamat_lengkap'             => 'required|string',
            'nama_kantor'                => 'required|string|max:255',
            'alamat_kantor'              => 'required|string',
            'keperluan'                  => 'required|string',
            'rt'                         => 'required|string|max:10',
            'rw'                         => 'required|string|max:10',
            'no_surat_pengantar'         => 'required|string|max:100',
            'tanggal_surat_pengantar'    => 'required|date',
            'spkdk_surat_pengantar_rtrw' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkdk_ktp_kk_pemohon'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkdk_struktur_organisasi'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkdk_bukti_pbb'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSprikRules(): array
    {
        return [
            'nama_lengkap'               => 'required|string|max:255',
            'nik_bersangkutan'           => 'required|string|size:16',
            'no_wa'                      => 'required|string|max:20',
            'tempat_lahir'               => 'required|string|max:100',
            'tanggal_lahir'              => 'required|date',
            'jenis_kelamin'              => 'required|in:Laki-laki,Perempuan',
            'alamat_lengkap'             => 'required|string',
            'nama_lembaga'               => 'required|string|max:255',
            'materi_kursus'              => 'required|string|max:255',
            'lama_kegiatan'              => 'required|string|max:100',
            'alamat_tempat_kegiatan'     => 'required|string|max:500',
            'rt'                         => 'required|string|max:10',
            'rw'                         => 'required|string|max:10',
            'no_surat_pengantar'         => 'required|string|max:100',
            'tanggal_surat_pengantar'    => 'required|date',
            'sprik_surat_pengantar_rtrw' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sprik_ktp_kk_pemohon'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sprik_ktp_penyelenggara'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sprik_bukti_pbb'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getRoipkRules(): array
    {
        return [
            'nama_lengkap'                => 'required|string|max:255',
            'nik_bersangkutan'            => 'required|string|size:16',
            'no_wa'                       => 'required|string|max:20',
            'jenis_kelamin'               => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'                => 'required|string|max:100',
            'tanggal_lahir'               => 'required|date',
            'alamat_lengkap'              => 'required|string',
            'nama_lembaga'                => 'required|string|max:255',
            'alamat_lembaga'              => 'required|string',
            'materi_lembaga'              => 'required|string',
            'lama_kegiatan'               => 'required|string|max:100',
            'no_surat_pengantar'          => 'required|string|max:100',
            'tanggal_surat_pengantar'     => 'required|date',
            'roipk_surat_pengantar_rtrw'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_ktp_kk_pemohon'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_surat_permohonan'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_struktur_organisasi'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_ijazah_kompetensi'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_izin_tetangga'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_daftar_fasilitas'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_silabus'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_surat_pengantar_lurah' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'roipk_bukti_pbb'             => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkdkRules(): array
    {
        return [
            'nama_lengkap'              => 'required|string|max:255',
            'nik_bersangkutan'          => 'required|string|size:16',
            'no_wa'                     => 'required|string|max:20',
            'jenis_kelamin'             => 'required|in:Laki-laki,Perempuan',
            'tempat_lahir'              => 'required|string|max:100',
            'tanggal_lahir'             => 'required|date',
            'alamat_lengkap'            => 'required|string',
            'nama_kantor'               => 'required|string|max:255',
            'alamat_kantor'             => 'required|string',
            'keperluan'                 => 'required|string|max:500',
            'rt'                        => 'required|string|max:10',
            'rw'                        => 'required|string|max:10',
            'no_surat_pengantar'        => 'required|string|max:100',
            'tanggal_surat_pengantar'   => 'required|date',
            'skdk_surat_pengantar_rtrw' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_ktp_pemohon'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_akta_kepengurusan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_akta_parpol'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_imb_sewa'             => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_surat_pengantar_lurah'=> 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdk_bukti_pbb'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkdkoRules(): array
    {
        return [
            'nama_lengkap'               => 'required|string|max:255',
            'nik_bersangkutan'           => 'required|string|size:16',
            'no_wa'                      => 'required|string|max:20',
            'rt'                         => 'required|string|max:10',
            'rw'                         => 'required|string|max:10',
            'no_surat_pengantar'         => 'required|string|max:100',
            'tanggal_surat_pengantar'    => 'required|date',
            'nama_kantor'                => 'required|string|max:255',
            'alamat_jalan'               => 'required|string|max:255',
            'keperluan'                  => 'required|string|max:500',
            'skdko_surat_pengantar_rtrw'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_ktp_kk_pemohon'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_sk_kepengurusan'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_bukti_pbb'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_akta_pendirian'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_npwp'                   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skdko_surat_pernyataan_warga' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkbRules(): array
    {
        return [
            // Data Diri Pemohon
            'nama_lengkap'            => 'required|string|max:255',
            'nik_bersangkutan'        => 'required|string|size:16',
            'no_wa'                   => 'required|string|max:20',
            'jenis_kelamin'           => 'required|in:Laki-laki,Perempuan',
            'warga_negara'            => 'required|string|max:50',
            'agama'                   => 'required|string|max:50',
            'tempat_lahir'            => 'required|string|max:100',
            'tanggal_lahir'           => 'required|date',
            'status_perkawinan'       => 'required|string',
            'pekerjaan'               => 'required|string|max:100',
            'alamat_lengkap'          => 'required|string',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Data Perjalanan
            'tujuan'                  => 'required|string|max:255',
            'keperluan'               => 'required|string|max:500',
            'hari_berangkat'          => 'required|string|max:20',
            'tanggal_berangkat'       => 'required|date',
            'pengikut'                => 'nullable|string',

            // Dokumen Lampiran
            'skb_surat_pengantar_rtrw' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skb_ktp_kk_pemohon'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skb_bukti_pbb'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSpkhRules(): array
    {
        return [
            // Data Diri Pemohon
            'nama_lengkap'            => 'required|string|max:255',
            'nik_bersangkutan'        => 'required|string|size:16',
            'no_wa'                   => 'required|string|max:20',
            'jenis_kelamin'           => 'required|in:Laki-laki,Perempuan',
            'warga_negara'            => 'required|string|max:50',
            'agama'                   => 'required|string|max:50',
            'tempat_lahir'            => 'required|string|max:100',
            'tanggal_lahir'           => 'required|date',
            'status_perkawinan'       => 'required|string',
            'pekerjaan'               => 'required|string|max:100',
            'alamat_lengkap'          => 'required|string',

            // Data Kehilangan
            'hari_kehilangan'         => 'required|string|max:20',
            'tanggal_kehilangan'      => 'required|date',
            'jam_kehilangan'          => 'required|string|max:10',
            'lokasi_kehilangan'       => 'required|string|max:255',
            'barang_hilang'           => 'required|string',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Dokumen Lampiran
            'spkh_surat_pengantar_rtrw' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkh_ktp_kk_pemohon'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkh_surat_pernyataan'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkh_ktp_saksi'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spkh_bukti_pbb'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkgRules()
    {
        return [
            // Data Diri Pemohon
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'no_wa'                 => 'required|string|max:20',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',
            'keperluan'             => 'required|string|max:255',

            // Data Orang Gaib
            'gaib_nama'             => 'required|string|max:255',
            'gaib_nik'              => 'required|string|size:16',
            'gaib_jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'gaib_agama'            => 'required|string',
            'gaib_tempat_lahir'     => 'required|string|max:100',
            'gaib_tanggal_lahir'    => 'required|date',
            'gaib_pekerjaan'        => 'required|string|max:100',
            'gaib_alamat'           => 'required|string',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'skg_surat_pengantar_rtrw'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skg_blangko_pernyataan'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skg_ktp_kk_bersangkutan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skg_ktp_saksi'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skg_bukti_lunas_pbb'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skg_buku_nikah'             => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSksiRules()
    {
        return [
            // Data Diri Pemohon
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'no_wa'                 => 'required|string|max:20',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',

            // Data Istri/Suami
            'istri_nama'             => 'required|string|max:255',
            'istri_nik'              => 'required|string|size:16',
            'istri_jenis_kelamin'    => 'required|in:Laki-laki,Perempuan',
            'istri_agama'            => 'required|string',
            'istri_tempat_lahir'     => 'required|string|max:100',
            'istri_tanggal_lahir'    => 'required|date',
            'istri_pekerjaan'        => 'required|string|max:100',
            'istri_alamat'           => 'required|string',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'sksi_surat_pengantar_rtrw'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sksi_blangko_pernyataan'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sksi_ktp_kk_bersangkutan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sksi_ktp_saksi'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sksi_bukti_lunas_pbb'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sksi_surat_pernyataan_penikah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkjdRules()
    {
        return [
            // Data Diri
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'no_wa'                 => 'required|string|max:20',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',
            'keperluan'             => 'required|string|max:255',

            // Data Orang Gaib dihapus sesuai permintaan

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'skjd_surat_pengantar_rtrw'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skjd_blangko_pernyataan'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skjd_ktp_kk_bersangkutan'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skjd_ktp_saksi'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skjd_bukti_lunas_pbb'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skjd_akta_kematian_perceraian' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkmRules()
    {
        return [
            // Surat Pengantar
            'nomor_pengantar' => 'required|string|max:50',
            'tanggal_pengantar' => 'required|date',
            'rt' => 'required|string|max:10',
            'rw' => 'required|string|max:10',

            // Jenazah
            'nama_jenazah' => 'required|string|max:255',
            'nik_jenazah' => 'required|string|size:16',
            'jk_jenazah' => 'required|in:L,P',
            'tempat_lahir_jenazah' => 'required|string|max:100',
            'tanggal_lahir_jenazah' => 'required|date',
            'alamat_jenazah' => 'required|string',
            'agama_jenazah' => 'required|string',
            'pekerjaan_jenazah' => 'required|string|max:100',

            // Detail Kematian
            'hari_meninggal' => 'required|string',
            'tanggal_meninggal' => 'required|date',
            'pukul_meninggal' => 'required|string',
            'tempat_meninggal' => 'required|string|max:255',
            'sebab_kematian' => 'required|string|max:255',
            'tempat_pemakaman' => 'required|string|max:255',

            // Pelapor
            'nama_pelapor' => 'required|string|max:255',
            'nik_pelapor' => 'required|string|size:16',
            'alamat_pelapor' => 'nullable|string',
            'hubungan_pelapor' => 'required|string|max:100',
            'no_wa' => 'required|string|max:20',

            // Dokumen Lampiran
            'skm_surat_pengantar'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skm_blangko_pernyataan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skm_ktp_kk_pemohon'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skm_ktp_kk_meninggal'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skm_ktp_saksi'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skm_bukti_pbb'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSktmRules()
    {
        return [
            // Data Diri
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',
            'no_wa'                 => 'required|string|max:20',
            'keperluan_sktm'        => 'required|string',
            'keterangan_sktm'       => 'required|string',

            // Surat Pengantar RT/RW
            'rt'                    => 'required|string|max:10',
            'rw'                    => 'required|string|max:10',
            'no_surat_pengantar'    => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            //'no_surat_pernyataan'      => 'required|string|max:100',
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'surat_pengantar_rtrw'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'blangko_pernyataan'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ktp_kk_bersangkutan'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ktp_saksi'                 => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'surat_rekomendasi_sekolah' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bukti_lunas_pbb'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSktmrRules()
    {
        return [
            // Data Diri
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'pendidikan_terakhir'   => 'required|string',
            'alamat_lengkap'        => 'required|string',
            'no_wa'                 => 'required|string|max:20',
            'keperluan'             => 'required|string|max:255',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            //'no_surat_pernyataan'      => 'required|string|max:100',
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'sktmr_surat_pengantar'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sktmr_blangko_pernyataan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sktmr_ktp_kk'            => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sktmr_ktp_saksi'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sktmr_bukti_pbb'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkpRules()
    {
        return [
            // Data Diri
            'nama_lengkap' => 'required|string|max:255',
            'nik_bersangkutan' => 'required|string|size:16',
            'jenis_kelamin' => 'required|string|in:Laki-laki,Perempuan',
            'agama' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'status_perkawinan' => 'required|string|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pekerjaan' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'no_wa' => 'required|string|max:20',
            'jumlah_penghasilan' => 'required|string', // atau numeric, tapi formnya mungkin bisa text / format ribuan
            'keperluan' => 'required|string',

            // Surat Pengantar
            'rt' => 'required|string',
            'rw' => 'required|string',
            'no_surat_pengantar' => 'required|string',
            'tanggal_surat_pengantar' => 'required|date',

            // Lampiran
            'skp_surat_pengantar' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'skp_blangko_pernyataan' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'skp_ktp_kk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'skp_ktp_saksi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'skp_bukti_pbb' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    private function getSkbmRules()
    {
        return [
            // Data Diri
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',
            'keperluan'             => 'required|string|max:255',

            // Surat Pengantar RT/RW
            'rt'                      => 'required|string|max:10',
            'rw'                      => 'required|string|max:10',
            'no_surat_pengantar'      => 'required|string|max:100',
            'tanggal_surat_pengantar' => 'required|date',

            // Surat Pernyataan
            //'no_surat_pernyataan'      => 'required|string|max:100',
            'tanggal_surat_pernyataan' => 'required|date',

            // Dokumen Lampiran
            'skbm_surat_pengantar'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skbm_blangko_pernyataan' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skbm_ktp_kk'             => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skbm_ktp_saksi'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skbm_bukti_pbb'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSkmhRules()
    {
        return [
            // Data Pemohon
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'jenis_kelamin'         => 'required|in:Laki-laki,Perempuan',
            'agama'                 => 'required|string',
            'kewarganegaraan'       => 'required|string',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'alamat_lengkap'        => 'required|string',

            // Data Ayah
            'ayah_nama'             => 'required|string|max:255',
            'ayah_bin'              => 'required|string|max:255',
            'ayah_nik'              => 'required|string|size:16',
            'ayah_agama'            => 'required|string',
            'ayah_kewarganegaraan'  => 'required|string',
            'ayah_tempat_lahir'     => 'required|string|max:100',
            'ayah_tanggal_lahir'    => 'required|date',
            'ayah_pekerjaan'        => 'required|string',
            'ayah_alamat'           => 'required|string',

            // Data Ibu
            'ibu_nama'              => 'required|string|max:255',
            'ibu_binti'             => 'required|string|max:255',
            'ibu_nik'               => 'required|string|size:16',
            'ibu_agama'             => 'required|string',
            'ibu_kewarganegaraan'   => 'required|string',
            'ibu_tempat_lahir'      => 'required|string|max:100',
            'ibu_tanggal_lahir'     => 'required|date',
            'ibu_pekerjaan'         => 'required|string',
            'ibu_alamat'            => 'required|string',

            // Dokumen Lampiran
            'skmh_surat_pengantar'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_akta_ijazah_catin'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_ktp_kk_catin'             => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_ktp_kk_ortu'              => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_pas_foto'                 => 'required|file|mimes:jpg,jpeg|max:5120',
            'skmh_ktp_saksi'                => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_form_n2_n5'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_akta_cerai_kematian'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_dispensasi_pengadilan'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_izin_atasan'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_izin_poligami'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_rekom_dp3a'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_surat_imunisasi_catin'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_bukti_pbb'                => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    private function getSdnhRules()
    {
        return [
            // Data Pemohon
            'nama_lengkap'          => 'required|string|max:255',
            'nik_bersangkutan'      => 'required|string|size:16',
            'tempat_lahir'          => 'required|string|max:100',
            'tanggal_lahir'         => 'required|date',
            'jenis_kelamin'         => 'required|in:LAKI-LAKI,PEREMPUAN',
            'agama'                 => 'required|string',
            'status_perkawinan'     => 'required|string',
            'pekerjaan'             => 'required|string|max:100',
            'no_wa'                 => 'required|string|max:20',
            'alamat_lengkap'        => 'required|string',

            // Data Pasangan
            'nama_pasangan'             => 'required|string|max:255',
            'tempat_lahir_pasangan'      => 'required|string|max:100',
            'tanggal_lahir_pasangan'     => 'required|date',
            'jenis_kelamin_pasangan'     => 'required|in:LAKI-LAKI,PEREMPUAN',
            'agama_pasangan'             => 'required|string',
            'status_perkawinan_pasangan' => 'required|string',
            'pekerjaan_pasangan'         => 'required|string|max:100',
            'alamat_pasangan'            => 'required|string',

            // Pelaksanaan
            'hari_pernikahan'    => 'required|string',
            'tanggal_pernikahan' => 'required|date',
            'pukul_pernikahan'   => 'required|string',
            'alamat_pernikahan'  => 'required|string',
            'alasan_dispensasi'  => 'required|string',

            // Dokumen Lampiran
            'sdnh_surat_pengantar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sdnh_ktp_kk'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sdnh_formulir_n'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sdnh_lunas_pbb'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sdnh_rekom_kua'       => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'sdnh_akta_cerai_mati' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute wajib diisi.',
            'size' => ':attribute harus :size karakter.',
            'max' => ':attribute maksimal :max karakter.',
            'in' => 'Pilihan :attribute tidak valid.',
            'exists' => ':attribute tidak ditemukan.',
        ];
    }
}
