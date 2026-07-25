<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Path Binary mysqldump
    |--------------------------------------------------------------------------
    |
    | Path ke executable `mysqldump`. Default 'mysqldump' mengandalkan PATH
    | (biasanya sudah tersedia di server Ubuntu setelah install mysql-server).
    | Di Laragon lokal (Windows) umumnya perlu path absolut, contoh:
    |   D:\laragon\bin\mysql\mysql-8.x.xx\bin\mysqldump.exe
    |
    */

    'mysqldump_path' => env('DB_DUMP_BINARY_PATH', 'mysqldump'),

    /*
    |--------------------------------------------------------------------------
    | Disk Penyimpanan Backup
    |--------------------------------------------------------------------------
    |
    | Disk tempat file backup disimpan. Gunakan 'local' (storage/app) agar
    | file berada di luar document root dan tidak bisa diakses via web.
    |
    */

    'disk' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Retensi File Backup
    |--------------------------------------------------------------------------
    |
    | Jumlah file yang dipertahankan PER JENIS (daily/weekly). Default 1 =
    | hanya simpan backup terbaru; file lama dihapus otomatis tiap command
    | jalan. Naikkan nilainya bila ingin menyimpan lebih banyak riwayat.
    |
    */

    'keep' => (int) env('BACKUP_KEEP', 1),

];
