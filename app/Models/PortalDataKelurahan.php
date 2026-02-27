<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalDataKelurahan extends Model
{
    use SoftDeletes;

    protected $table = 'portal_data_kelurahans';

    protected $fillable = [
        'kelurahan_id',
        'kategori',
        'nama',
        'keterangan',
        'alamat',
        'latitude',
        'longitude',
        'foto',
    ];

    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relasi ke model Kelurahan.
     */
    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class, 'kelurahan_id');
    }

    /**
     * Scope: data yang memiliki koordinat (untuk ditampilkan di peta).
     */
    public function scopeHasKoordinat($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /**
     * Label yang lebih manusiawi untuk tiap kategori.
     */
    public static function labelKategori(): array
    {
        return [
            'rw'                  => 'Ketua RW',
            'rt'                  => 'Ketua RT',
            'lpm'                 => 'Lembaga Pemberdayaan Masyarakat',
            'tempat_ibadah'       => 'Tempat Ibadah',
            'pemakaman'           => 'Tempat Pemakaman',
            'sarana_pendidikan'   => 'Sarana Pendidikan',
            'fasilitas_kesehatan' => 'Fasilitas Kesehatan',
            'fasilitas_keamanan'  => 'Fasilitas Keamanan',
            'pos_kamling'         => 'Pos Kamling',
        ];
    }

    /**
     * Ikon Leaflet per kategori (untuk peta).
     */
    public static function ikonKategori(): array
    {
        return [
            'rw'                  => '🏘️',
            'rt'                  => '🏠',
            'lpm'                 => '🏛️',
            'tempat_ibadah'       => '🕌',
            'pemakaman'           => '⚰️',
            'sarana_pendidikan'   => '🏫',
            'fasilitas_kesehatan' => '🏥',
            'fasilitas_keamanan'  => '🚔',
            'pos_kamling'         => '🏚️',
        ];
    }
}
