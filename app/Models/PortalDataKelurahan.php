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
        'jenis_fasilitas',
        'status_fasilitas',
        'rt',
        'rw',
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
            'fasilitas_umum'      => 'Fasilitas Umum',
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
            'fasilitas_umum'      => '🛝',
        ];
    }

    /**
     * Opsi sub-kategori dinamis / statis berdasarkan array.
     */
    public static function opsiJenisIbadah(): array
    {
        return ['Masjid', 'Langgar/Musholla', 'Gereja', 'Pura', 'Vihara', 'Kelenteng/Lintang', 'Sinagoge'];
    }

    public static function opsiJenisPemakaman(): array
    {
        return ['Tempat Pemakaman Umum (TPU)', 'Tempat Pemakaman Bukan Umum (TPBU)/ Swasta', 'Tempat Pemakaman Khusus (TPK)', 'Makam Keluarga/Pribadi', 'Tempat Kremasi & Abu (Cremation/Urn Garden)'];
    }

    public static function opsiJenisPendidikan(): array
    {
        return ['PAUD', 'TK', 'SD', 'SMP', 'SMA', 'Perguruan Tinggi'];
    }

    public static function opsiJenisKesehatan(): array
    {
        return ['Puskesmas', 'Klinik Pratama', 'Tempat Praktik Mandiri', 'Posyandu', 'Rumah Sakit', 'Klinik Utama (Klinik Spesialis)', 'Apotek', 'Laboratorium Kesehatan', 'Unit Transfusi Darah', 'Optikal', 'Fasilitas Kesehatan Tradisional'];
    }

    public static function opsiJenisKeamanan(): array
    {
        return ['TNI', 'POLRI', 'Swadaya Masyarakat'];
    }

    public static function opsiStatusFasilitas(): array
    {
        return ['Negeri', 'Swasta'];
    }
}
