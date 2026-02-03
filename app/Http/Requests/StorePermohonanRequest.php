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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $commonRules = [
            'jenis_surat_id' => 'required|exists:jenis_surats,id',
            'kelurahan_id' => 'required|exists:m_kelurahans,id',

            'pemohon_nama' => 'required|string|max:255',
            'pemohon_nik' => 'required|string|size:16',
            'pemohon_phone' => 'required|string|max:20',
            'pemohon_alamat' => 'required|string|max:255',
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
            case 'SKU': // Surat Keterangan Usaha (Example)
                // $specificRules = $this->getSkuRules();
                break;
            default:
                // Default rules or empty
                break;
        }

        return array_merge($commonRules, $specificRules);
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
            'hubungan_pelapor' => 'required|string|max:100',
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
