<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePermohonanRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'catatan' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'catatan.max' => 'Catatan maksimal 500 karakter',
        ];
    }
}
