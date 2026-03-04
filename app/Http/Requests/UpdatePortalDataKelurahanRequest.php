<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortalDataKelurahanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelurahan_id'     => 'sometimes|required|exists:m_kelurahans,id',
            'kategori'         => 'sometimes|required|in:rw,rt,lpm,tempat_ibadah,pemakaman,sarana_pendidikan,fasilitas_kesehatan,fasilitas_keamanan,pos_kamling,fasilitas_umum',
            'jenis_fasilitas'  => 'nullable|string|max:255',
            'status_fasilitas' => 'nullable|string|max:255',
            'rt'               => 'nullable|string|max:10',
            'rw'               => 'nullable|string|max:10',
            'nama'             => 'sometimes|required|string|max:255',
            'keterangan'       => 'nullable|string',
            'alamat'           => 'nullable|string|max:500',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.exists'   => 'Kelurahan tidak ditemukan.',
            'kategori.required'     => 'Kategori wajib dipilih.',
            'kategori.in'           => 'Kategori tidak valid.',
            'nama.required'         => 'Nama wajib diisi.',
            'nama.max'              => 'Nama maksimal 255 karakter.',
            'alamat.max'            => 'Alamat maksimal 500 karakter.',
            'latitude.numeric'      => 'Latitude harus berupa angka.',
            'latitude.between'      => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric'     => 'Longitude harus berupa angka.',
            'longitude.between'     => 'Longitude harus antara -180 dan 180.',
            'foto.image'            => 'Foto harus berupa gambar.',
            'foto.mimes'            => 'Format foto: jpg, jpeg, png, atau webp.',
            'foto.max'              => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
