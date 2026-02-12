<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApprovalFlowRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_surat_id' => 'sometimes|required|exists:jenis_surats,id',
            'kelurahan_id' => 'sometimes|required|exists:m_kelurahans,id',
            'require_kecamatan_approval' => 'boolean',
            'require_kabupaten_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'jenis_surat_id.required' => 'Jenis surat wajib dipilih',
            'jenis_surat_id.exists' => 'Jenis surat tidak valid',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih',
            'kelurahan_id.exists' => 'Kelurahan tidak valid',
        ];
    }
}
