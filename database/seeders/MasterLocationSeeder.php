<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MasterLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Kabupatens
        $this->command->info('Seeding Master Kabupaten...');
        $kabupatenPath = storage_path('app/public/data_indonesia/Data Kabupaten.json');
        
        if (File::exists($kabupatenPath)) {
            $kabupatens = json_decode(File::get($kabupatenPath), true);
            $chunks = array_chunk($kabupatens, 100);
            
            foreach ($chunks as $chunk) {
                $data = [];
                foreach ($chunk as $item) {
                    $data[] = [
                        'id' => $item['city_id'],
                        'nama' => $item['name'],
                        'kode' => $item['city_id'], // Using ID as code since no code in JSON
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('m_kabupatens')->insertOrIgnore($data);
            }
        }

        // 2. Seed Kecamatans
        $this->command->info('Seeding Master Kecamatan...');
        $kecamatanPath = storage_path('app/public/data_indonesia/Data Kecamatan.json');
        
        if (File::exists($kecamatanPath)) {
            $kecamatans = json_decode(File::get($kecamatanPath), true);
            $chunks = array_chunk($kecamatans, 100);
            
            foreach ($chunks as $chunk) {
                $data = [];
                foreach ($chunk as $item) {
                    $data[] = [
                        'id' => $item['districts_id'],
                        'kabupaten_id' => $item['city_id'],
                        'nama' => $item['name'],
                        'kode' => $item['districts_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('m_kecamatans')->insertOrIgnore($data);
            }
        }

        // 3. Seed Kelurahans
        $this->command->info('Seeding Master Kelurahan...');
        $kelurahanPath = storage_path('app/public/data_indonesia/Data Desa.json');
        
        if (File::exists($kelurahanPath)) {
            // Using File::get might crash for large files, but 8MB might be okay.
            // If it crashes, we should use a stream reader.
            // For now, let's try reading it all as it's < 10MB.
            $kelurahans = json_decode(File::get($kelurahanPath), true);
            $chunks = array_chunk($kelurahans, 500); // Larger chunk for faster insert
            
            foreach ($chunks as $chunk) {
                $data = [];
                foreach ($chunk as $item) {
                    $data[] = [
                        'id' => $item['village_id'],
                        'kecamatan_id' => $item['districts_id'],
                        'nama' => $item['name'],
                        'kode' => $item['village_id'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('m_kelurahans')->insertOrIgnore($data);
            }
        }
        
        $this->command->info('Master Location Seeding Completed!');
    }
}
