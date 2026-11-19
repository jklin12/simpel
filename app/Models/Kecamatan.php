<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    use HasFactory;

    protected $table = 'm_kecamatans';
    protected $guarded = ['id'];

    public function getSignerJabatanAttribute(): string
    {
        return match ($this->status_pejabat) {
            'Plh' => 'Plh. Camat',
            'Plt' => 'Plt. Camat',
            default => 'Camat',
        };
    }

    public function getSignerTitleAttribute(): string
    {
        return $this->signer_jabatan . ' ' . ucwords(strtolower($this->nama));
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    public function kelurahans(): HasMany
    {
        return $this->hasMany(Kelurahan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
