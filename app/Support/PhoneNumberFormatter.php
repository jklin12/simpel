<?php

namespace App\Support;

class PhoneNumberFormatter
{
    /**
     * Normalisasi nomor telepon Indonesia ke format internasional (62...)
     * tanpa tanda plus, sesuai kebutuhan tautan wa.me.
     */
    public static function normalizeIndonesian(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }
}
