<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,

            // Roles
            'roles' => $this->roles->pluck('name'),
            'primary_role' => $this->roles->first()?->name,

            // Location
            'location' => [
                'kabupaten' => [
                    'id' => $this->kabupaten?->id,
                    'nama' => $this->kabupaten?->nama,
                    'kode' => $this->kabupaten?->kode,
                ],
                'kecamatan' => [
                    'id' => $this->kecamatan?->id,
                    'nama' => $this->kecamatan?->nama,
                    'kode' => $this->kecamatan?->kode,
                ],
                'kelurahan' => [
                    'id' => $this->kelurahan?->id,
                    'nama' => $this->kelurahan?->nama,
                    'kode' => $this->kelurahan?->kode,
                ],
            ],

            // Timestamps
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_human' => $this->created_at->diffForHumans(),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'updated_at_human' => $this->updated_at->diffForHumans(),
        ];
    }
}
