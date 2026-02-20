<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class PermohonanSurat extends Model
{
    use HasFactory, Notifiable;

    /**
     * Route notifications for the WhatsApp channel.
     */
    public function routeNotificationForWhatsapp()
    {
        return $this->phone_pemohon;
    }

    protected $guarded = ['id'];

    protected $casts = [
        'data_permohonan' => 'array',
        'tanggal_surat' => 'date',
        'completed_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function approvalFlow(): BelongsTo
    {
        return $this->belongsTo(ApprovalFlow::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(PermohonanApproval::class);
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(PermohonanDokumen::class);
    }

    public function currentApprovalStep(): BelongsTo
    {
        return $this->belongsTo(ApprovalStep::class, 'current_step', 'step_order');
        // Note: Relation logic depends on how we define current_step. 
        // If it's step_order integer it's not a direct FK to ID. 
        // Helper method might be better.
    }
}
