<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'm_kelurahans';
    protected $guarded = ['id'];

    public function getSignerTitleAttribute(): string
    {
        $baseTitle = 'Lurah ' . ucwords(strtolower($this->nama));

        if ($this->status_pejabat === 'Plh') {
            return 'Plh. ' . $baseTitle;
        }

        if ($this->status_pejabat === 'Plt') {
            return 'Plt. ' . $baseTitle;
        }

        return $baseTitle;
    }
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function approvalFlows(): HasMany
    {
        return $this->hasMany(ApprovalFlow::class);
    }

    public function suratCounters(): HasMany
    {
        return $this->hasMany(SuratCounter::class);
    }
}
