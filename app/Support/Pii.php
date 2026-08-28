<?php

namespace App\Support;

/**
 * Util masking PII (Personally Identifiable Information) terpusat.
 *
 * Satu-satunya sumber logika penyamaran nilai yang ditampilkan di halaman admin.
 * Nilai penuh tidak pernah dikirim ke HTML awal — hanya versi tersamar dari sini.
 * Reveal nilai penuh dilakukan lewat endpoint terpisah (super_admin + audit log).
 */
class Pii
{
    public const BULLET = '•';

    /**
     * Samarkan NIK: tampilkan 4 digit awal + 4 digit akhir, tengah disamarkan.
     * Contoh: 3671010203041234 → 3671••••••••1234
     */
    public static function maskNik(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '-';
        }

        $len = mb_strlen($value);
        if ($len <= 8) {
            // Terlalu pendek untuk menampilkan awal+akhir tanpa membuka mayoritas.
            return str_repeat(self::BULLET, max($len, 4));
        }

        $middle = str_repeat(self::BULLET, $len - 8);

        return mb_substr($value, 0, 4) . $middle . mb_substr($value, -4);
    }

    /**
     * Samarkan nomor telepon: tampilkan 4 digit awal + 3 digit akhir.
     * Contoh: 081234567890 → 0812•••••890
     */
    public static function maskPhone(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '-';
        }

        $len = mb_strlen($value);
        if ($len <= 5) {
            return str_repeat(self::BULLET, max($len - 2, 2)) . mb_substr($value, -2);
        }

        $middle = str_repeat(self::BULLET, max($len - 7, 3));

        return mb_substr($value, 0, 4) . $middle . mb_substr($value, -3);
    }

    /**
     * Samarkan alamat: sisakan sedikit awal sebagai hint, sisanya disamarkan.
     * Contoh: "Jl. Merdeka No. 10 RT 02" → "Jl. Me•••••••••"
     */
    public static function maskAddress(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '-';
        }

        $hint = mb_substr($value, 0, 6);

        return $hint . str_repeat(self::BULLET, 9);
    }

    /**
     * Samarkan nilai generik (PII yang tidak masuk tipe spesifik).
     * Sisakan 2 karakter awal, sisanya disamarkan.
     */
    public static function maskGeneric(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '-';
        }

        $len = mb_strlen($value);
        if ($len <= 2) {
            return str_repeat(self::BULLET, $len);
        }

        return mb_substr($value, 0, 2) . str_repeat(self::BULLET, min($len - 2, 10));
    }

    /**
     * Dispatcher: samarkan $value berdasarkan $type (nik|phone|address|generic).
     */
    public static function mask(?string $value, string $type = 'generic'): string
    {
        switch ($type) {
            case 'nik':
                return self::maskNik($value);
            case 'phone':
                return self::maskPhone($value);
            case 'address':
                return self::maskAddress($value);
            default:
                return self::maskGeneric($value);
        }
    }

    /**
     * Tebak tipe masking dari nama key (untuk field dinamis data_permohonan).
     */
    public static function inferType(string $key): string
    {
        $key = strtolower($key);

        if (self::keyMatches($key, ['nik', 'ktp', 'no_kk', 'nomor_kk'])) {
            return 'nik';
        }
        if (self::keyMatches($key, ['alamat', 'domisili'])) {
            return 'address';
        }
        if (self::keyMatches($key, ['phone', 'hp', 'telp', 'telepon', 'wa', 'whatsapp'])) {
            return 'phone';
        }

        return 'generic';
    }

    /**
     * Apakah key ini termasuk PII yang harus disamarkan?
     * Gabungan: daftar eksplisit dari config + pencocokan pola.
     */
    public static function isPiiKey(string $key): bool
    {
        $key = strtolower($key);

        $explicit = array_map('strtolower', (array) config('pii.data_keys', []));
        if (in_array($key, $explicit, true)) {
            return true;
        }

        $patterns = (array) config('pii.data_patterns', ['nik', 'ktp', 'alamat', 'domisili', 'phone', 'hp', 'telp', 'telepon']);

        return self::keyMatches($key, $patterns);
    }

    /**
     * True bila $key mengandung salah satu $needles (substring, case-insensitive).
     */
    private static function keyMatches(string $key, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($key, strtolower($needle))) {
                return true;
            }
        }

        return false;
    }
}
