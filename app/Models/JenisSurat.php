<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSurat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'required_fields' => 'array',
        'is_active' => 'boolean',
    ];

    public function approvalFlows(): HasMany
    {
        return $this->hasMany(ApprovalFlow::class);
    }

    public function suratCounters(): HasMany
    {
        return $this->hasMany(SuratCounter::class);
    }

    public function permohonanSurats(): HasMany
    {
        return $this->hasMany(PermohonanSurat::class);
    }
}
