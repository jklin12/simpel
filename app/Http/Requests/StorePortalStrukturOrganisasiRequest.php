<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortalStrukturOrganisasiRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama'      => ['required', 'string', 'max:255'],
            'jabatan'   => ['required', 'string', 'max:255'],
            'foto'      => ['nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg,webp'],
            'parent_id' => ['nullable', 'exists:portal_struktur_organisasis,id'],
            'urutan'    => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required'    => 'Nama wajib diisi.',
            'jabatan.required' => 'Jabatan wajib diisi.',
            'foto.image'       => 'File foto harus berupa gambar.',
            'foto.max'         => 'Ukuran foto maksimal 2MB.',
            'parent_id.exists' => 'Atasan langsung yang dipilih tidak valid atau sudah dihapus.',
            'urutan.required'  => 'Urutan tampil wajib diisi.',
            'urutan.integer'   => 'Urutan harus berupa angka bulat.',
        ];
    }
}
