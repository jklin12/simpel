<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core Infrastructure
            MasterLocationSeeder::class,
            UserRoleSeeder::class,
            LurahSeeder::class,
            NotificationSeeder::class,
            SuratCounterSeeder::class,
            PekerjaanSeeder::class,

            // Letter Definitions & Workflows
            JenisSuratSeeder::class,
            ApprovalFlowSeeder::class,

            // Letter Specific Configuration (Fields, OCR, PDF Templates)
            SkpSeeder::class,
            SkmhSeeder::class,
            SkjdSeeder::class,
            SksiSeeder::class,
            SkgSeeder::class,
            SdnhSeeder::class,
            SktmrSeeder::class,
        ]);
    }
}
