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
            case 'SKTMR': // Surat Keterangan Tidak Memiliki Rumah
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
            case 'SKU': // Surat Keterangan Usaha (Example)
                // $specificRules = $this->getSkuRules();
                break;
            default:
                if ($jenisSurat->required_fields) {
                    foreach ($jenisSurat->required_fields as $field) {
                        $specificRules[$field['name']] = match ($field['type']) {
                            'file'   => ($field['is_required'] ? 'required' : 'nullable') . '|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
            'skmh_bukti_pbb',
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
            'surat_rekomendasi_sekolah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
            'skmh_pas_foto'                 => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_ktp_saksi'                => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_form_n2_n5'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_akta_cerai_kematian'      => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_dispensasi_pengadilan'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_izin_atasan'              => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_izin_poligami'            => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_rekom_dp3a'               => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'skmh_bukti_pbb'                => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
