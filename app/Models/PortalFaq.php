<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalFaq extends Model
{
    protected $table = 'portal_faqs';

    protected $fillable = [
        'pertanyaan',
        'jawaban',
        'kategori',
        'urutan',
        'status',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')->orderBy('urutan');
    }

    public static function kategoriOptions(): array
    {
        return [
            'umum'      => 'Umum',
            'surat'     => 'Layanan Surat',
            'kependudukan' => 'Kependudukan',
            'perizinan' => 'Perizinan',
            'lainnya'   => 'Lainnya',
        ];
    }
}
