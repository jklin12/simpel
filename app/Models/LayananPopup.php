<?php

namespace App\Models;

use App\Support\PhoneNumberFormatter;
use Illuminate\Database\Eloquent\Model;

class LayananPopup extends Model
{
    protected $table = 'layanan_popups';

    protected $fillable = [
        'gambar',
        'wa_number',
        'wa_message',
        'button_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getNormalizedWaNumberAttribute(): string
    {
        return PhoneNumberFormatter::normalizeIndonesian($this->wa_number);
    }
}
