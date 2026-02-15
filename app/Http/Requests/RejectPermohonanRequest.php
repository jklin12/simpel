<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPermohonanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'rejected_reason' => 'required|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'rejected_reason.required' => 'Alasan penolakan wajib diisi',
            'rejected_reason.max' => 'Alasan penolakan maksimal 500 karakter',
        ];
    }
}
