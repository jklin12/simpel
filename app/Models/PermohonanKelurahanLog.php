<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat perpindahan permohonan antar kelurahan (salah pilih wilayah).
 */
class PermohonanKelurahanLog extends Model
{
    protected $fillable = [
        'permohonan_surat_id',
        'from_kelurahan_id',
        'to_kelurahan_id',
        'moved_by',
        'alasan',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class, 'permohonan_surat_id');
    }

    public function fromKelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'from_kelurahan_id');
    }

    public function toKelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class, 'to_kelurahan_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}
