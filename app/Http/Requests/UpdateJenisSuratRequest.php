<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisSuratRequest extends FormRequest
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
        $jenisSuratId = $this->route('jenis_surat');

        return [
            'nama' => 'sometimes|required|string|max:255',
            'kode' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                Rule::unique('jenis_surats')->ignore($jenisSuratId)
            ],
            'deskripsi' => 'nullable|string',
            'required_fields' => 'nullable|array',
            'required_fields.*' => 'string',
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
            'required_fields.array' => 'Format required fields tidak valid',
            'template_path.max' => 'Path template maksimal 255 karakter',
        ];
    }
}
