<?php

/**
 * Pengelompokan jenis surat pada halaman publik (Daftar Layanan).
 * Hanya tampilan: tidak mengubah data jenis_surats. Kode yang belum terdaftar
 * di sini otomatis masuk kategori "Lainnya".
 */
return [
    'kategori' => [
        'keterangan' => [
            'label' => 'Keterangan warga',
            'tone' => 'bg-blue-50 text-blue-700',
            'kode' => ['SKTM', 'SKTMR', 'SKBM', 'SKP', 'SKJD', 'SKSI', 'SKG', 'SKB'],
        ],
        'kematian' => [
            'label' => 'Kematian & kehilangan',
            'tone' => 'bg-gray-100 text-gray-700',
            'kode' => ['SKM', 'SPKH'],
        ],
        'pernikahan' => [
            'label' => 'Pernikahan',
            'tone' => 'bg-rose-50 text-rose-700',
            'kode' => ['SPN', 'SDNH'],
        ],
        'lembaga' => [
            'label' => 'Lembaga & organisasi',
            'tone' => 'bg-amber-50 text-amber-700',
            'kode' => ['SKDKO', 'SKDK', 'SPKDK', 'SPRIK', 'ROIPK'],
        ],
    ],

    /**
     * Kode jenis surat (huruf kecil, sesuai nama partial di types/) yang memakai form wizard bertahap.
     * Jenis lain tetap memakai form satu halaman sampai wizard-nya diuji. Tambahkan kode di sini untuk mengaktifkan.
     */
    'wizard_aktif' => ['sktm', 'sktmr', 'skbm', 'skp', 'skjd'],

    'kategori_lainnya' => [
        'label' => 'Lainnya',
        'tone' => 'bg-primary-50 text-primary-700',
    ],
];
