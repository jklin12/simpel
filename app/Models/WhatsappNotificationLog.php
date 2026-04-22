<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappNotificationLog extends Model
{
    protected $fillable = [
        'permohonan_id',
        'notification_type',
        'notifiable_type',
        'notifiable_id',
        'phone_to',
        'message_preview',
        'has_file',
        'status',
        'error_message',
        'response_code',
        'attempt',
        'sent_at',
    ];

    protected $casts = [
        'has_file' => 'boolean',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class);
    }
}
