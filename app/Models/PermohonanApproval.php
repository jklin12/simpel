<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanApproval extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function permohonanSurat(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class);
    }

    // public function step()
    // {
    //     return $this->belongsTo(ApprovalStep::class, 'approval_step_id');
    // } 
    // Commented out as column is dropped

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
