<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePortalBeritaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'     => 'required|string|max:255',
            'konten'    => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'    => 'required|in:draft,published',
        ];
    }

    public function messages(): array
    {
        return [
            'judul.required'     => 'Judul berita wajib diisi.',
            'judul.max'          => 'Judul maksimal 255 karakter.',
            'konten.required'    => 'Konten berita wajib diisi.',
            'status.required'    => 'Status berita wajib dipilih.',
            'status.in'          => 'Status hanya boleh draft atau published.',
            'thumbnail.image'    => 'Thumbnail harus berupa gambar.',
            'thumbnail.mimes'    => 'Format thumbnail: jpg, jpeg, png, atau webp.',
            'thumbnail.max'      => 'Ukuran thumbnail maksimal 2 MB.',
        ];
    }
}
