<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisSuratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20|unique:jenis_surats,kode',
            'deskripsi' => 'nullable|string',
            'attachment_guides'                     => 'nullable|array',
            'attachment_guides.*.keterangan'        => 'nullable|string|max:500',
            'attachment_guides.*.contoh'            => 'nullable|string|max:500',
            'ocr_rules' => 'nullable|string',
            'template_path' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'nama.required' => 'Nama jenis surat wajib diisi',
            'nama.max' => 'Nama jenis surat maksimal 255 karakter',
            'kode.required' => 'Kode surat wajib diisi',
            'kode.max' => 'Kode surat maksimal 20 karakter',
            'kode.unique' => 'Kode surat sudah digunakan',
            'template_path.max' => 'Path template maksimal 255 karakter',
        ];
    }
}
