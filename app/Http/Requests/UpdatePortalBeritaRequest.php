<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortalBeritaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul'     => 'sometimes|required|string|max:255',
            'konten'    => 'sometimes|required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'    => 'sometimes|required|in:draft,published',
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
