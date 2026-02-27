<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSlider extends Model
{
    protected $table = 'portal_sliders';

    protected $fillable = [
        'judul',
        'sub_judul',
        'gambar',
        'warna_tema',
        'label_cta_1',
        'url_cta_1',
        'label_cta_2',
        'url_cta_2',
        'urutan',
        'status',
    ];

    /**
     * Scope untuk slider yang aktif, diurut berdasarkan urutan.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')->orderBy('urutan');
    }

    /**
     * Getter gradient background sesuai warna_tema.
     */
    public function getGradientAttribute(): string
    {
        return match ($this->warna_tema) {
            'green'  => 'from-emerald-800 via-teal-700 to-cyan-800',
            'orange' => 'from-orange-600 via-rose-600 to-pink-700',
            default  => 'from-primary-800 via-primary-700 to-primary-900',
        };
    }

    public static function warnaOptions(): array
    {
        return [
            'blue'   => 'Biru (Default)',
            'green'  => 'Hijau Emerald',
            'orange' => 'Oranye / Merah',
        ];
    }
}
