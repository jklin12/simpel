<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit log setiap akses "reveal" nilai PII penuh di halaman admin.
 * Ditulis oleh PiiRevealController pada saat super_admin membuka nilai penuh.
 */
class PiiAccessLog extends Model
{
    public const UPDATED_AT = null; // hanya created_at

    protected $fillable = [
        'user_id',
        'source',
        'subject_id',
        'field',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
