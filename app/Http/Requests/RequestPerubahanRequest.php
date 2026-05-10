<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestPerubahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'alasan.required' => 'Alasan perubahan wajib diisi.',
            'alasan.min'      => 'Alasan minimal 10 karakter.',
            'alasan.max'      => 'Alasan maksimal 1000 karakter.',
        ];
    }
}
