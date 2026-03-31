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
            MasterLocationSeeder::class,
            UserRoleSeeder::class,
            LurahSeeder::class,
            NotificationSeeder::class,
            SkpSeeder::class,
            SkmhSeeder::class,
            SkjdSeeder::class,
            SksiSeeder::class,
            SkgSeeder::class,
        ]);
    }
}
