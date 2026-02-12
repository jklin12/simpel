<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user');

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'sometimes|required|exists:roles,name',
            //'kabupaten_id' => 'nullable|exists:m_kabupatens,id',
            'kecamatan_id' => 'nullable|exists:m_kecamatans,id',
            'kelurahan_id' => 'nullable|exists:m_kelurahans,id',
            'phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role.required' => 'Role wajib dipilih',
            'role.exists' => 'Role tidak valid',
            'kabupaten_id.exists' => 'Kabupaten tidak valid',
            'kecamatan_id.exists' => 'Kecamatan tidak valid',
            'kelurahan_id.exists' => 'Kelurahan tidak valid',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
        ];
    }
}
