<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTemplateSuratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_surat_id' => ['required', 'integer', 'exists:jenis_surats,id'],
            'judul'          => ['required', 'string', 'max:255'],
            'deskripsi'      => ['nullable', 'string', 'max:1000'],
            'file'           => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'syarat'         => ['nullable', 'array'],
            'syarat.*'       => ['nullable', 'string', 'max:500'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'jenis_surat_id' => 'Jenis Surat',
            'judul'          => 'Judul Template',
            'file'           => 'File Template',
            'syarat.*'       => 'Syarat',
        ];
    }
}
