<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanDokumen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Daftar jenis dokumen yang dikenal sistem.
     */
    public const JENIS_DOKUMEN = [
        'ktp' => 'Foto KTP',
        'kk' => 'Foto KK',
        'surat_pengantar_rt' => 'Surat Pengantar RT',
        'foto_rumah' => 'Foto Rumah',
        'foto_usaha' => 'Foto Usaha',
        'akta_kelahiran' => 'Akta Kelahiran',
        'surat_pernyataan' => 'Surat Pernyataan',
        'surat_bidan' => 'Surat Bidan',
        'surat_rs' => 'Surat Rumah Sakit',
        'akta_pendirian' => 'Akta Pendirian',
        'ktp_alm' => 'KTP Almarhum',
        'ktp_ortu' => 'KTP Orang Tua',
        'dokumen_lainnya' => 'Dokumen Lainnya',
    ];

    public function permohonanSurat(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class);
    }

    /**
     * Get label for jenis_dokumen.
     */
    public function getLabelAttribute(): string
    {
        return self::JENIS_DOKUMEN[$this->jenis_dokumen] ?? ucwords(str_replace('_', ' ', $this->jenis_dokumen));
    }
}
