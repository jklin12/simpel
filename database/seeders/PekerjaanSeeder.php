<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('list pekerjaan.txt');
        if (file_exists($filePath)) {
            $jobs = array_map('trim', file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            $jobs = array_unique($jobs);

            foreach ($jobs as $job) {
                if (!empty($job)) {
                    \App\Models\Pekerjaan::firstOrCreate(['nama' => $job]);
                }
            }
        }
    }
}
