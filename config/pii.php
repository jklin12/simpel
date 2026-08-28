<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role yang boleh melihat PII penuh (reveal)
    |--------------------------------------------------------------------------
    | Role di luar daftar ini hanya melihat nilai tersamar tanpa tombol reveal.
    */
    'reveal_roles' => ['super_admin'],

    /*
    |--------------------------------------------------------------------------
    | Kolom PII langsung di tabel permohonan_surats
    |--------------------------------------------------------------------------
    | Dipakai sebagai whitelist endpoint reveal (source: permohonan) sekaligus
    | peta tipe masking-nya.
    */
    'permohonan_columns' => [
        'nik_pemohon'    => 'nik',
        'alamat_pemohon' => 'address',
        'phone_pemohon'  => 'phone',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kolom PII pada log WhatsApp (source: wa_log)
    |--------------------------------------------------------------------------
    */
    'wa_log_columns' => [
        'phone_to' => 'phone',
    ],

    /*
    |--------------------------------------------------------------------------
    | Key PII eksplisit di dalam data_permohonan (JSON)
    |--------------------------------------------------------------------------
    | Jaring pengaman untuk key bernama tak lazim yang tidak tertangkap pola
    | di bawah. Deteksi utama tetap berbasis pola (data_patterns).
    */
    'data_keys' => [
        'nik_jenazah',
        'alamat_jenazah',
        'gaib_nik',
        'gaib_alamat',
        'istri_nik',
        'istri_alamat',
        'suami_nik',
        'suami_alamat',
        'ayah_nik',
        'ayah_alamat',
        'ibu_nik',
        'ibu_alamat',
        'nama_pasangan',
        'alamat_pasangan',
        'pasangan_nik',
    ],

    /*
    |--------------------------------------------------------------------------
    | Pola substring nama key yang dianggap PII di data_permohonan
    |--------------------------------------------------------------------------
    | Cocok bila nama key MENGANDUNG salah satu string ini (case-insensitive).
    | Mencakup ke-17 jenis surat secara otomatis selama penamaan field wajar.
    */
    'data_patterns' => [
        'nik',
        'ktp',
        'alamat',
        'domisili',
        'phone',
        'hp',
        'telp',
        'telepon',
    ],

];
