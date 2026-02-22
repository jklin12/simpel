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
     * Convert comma-separated options strings to arrays before validation.
     */
    protected function prepareForValidation()
    {
        if ($this->has('required_fields') && is_array($this->required_fields)) {
            $fields = array_map(function ($field) {
                if (isset($field['options']) && is_string($field['options'])) {
                    $field['options'] = $field['options'] !== ''
                        ? array_map('trim', explode(',', $field['options']))
                        : null;
                }
                return $field;
            }, $this->required_fields);
            $this->merge(['required_fields' => $fields]);
        }
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
            'required_fields' => 'nullable|array',
            'required_fields.*.name' => 'required|string|regex:/^[a-z_]+$/',
            'required_fields.*.label' => 'required|string|max:100',
            'required_fields.*.type' => 'required|in:text,textarea,date,number,select,file',
            'required_fields.*.is_required' => 'nullable|boolean',
            'required_fields.*.options' => 'nullable|array',
            'required_fields.*.options.*' => 'string',
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
