<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TemplateSurat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'syarat'    => 'array',
        'is_active' => 'boolean',
    ];

    public function jenisSurat(): BelongsTo
    {
        return $this->belongsTo(JenisSurat::class);
    }

    /**
     * URL publik file template.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
