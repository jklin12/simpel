<?php

namespace Database\Seeders;

use App\Models\Kelurahan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PopulateKelurahanGeojsonPathSeeder extends Seeder
{
    /**
     * Parse GeoJSON file dan insert path ke m_kelurahans.geojson_path.
     * Setiap kelurahan mendapat feature-nya dalam file terpisah.
     */
    public function run(): void
    {
        // Path ke GeoJSON utama
        $sourceFile = storage_path('63.72_Kota_Banjarbaru/63.72_kelurahan.geojson');

        if (!file_exists($sourceFile)) {
            $this->command->error("File not found: $sourceFile");
            return;
        }

        // Parse GeoJSON
        $content = file_get_contents($sourceFile);
        $geoJsonData = json_decode($content, true);

        if (!isset($geoJsonData['features']) || !is_array($geoJsonData['features'])) {
            $this->command->error('Invalid GeoJSON format');
            return;
        }

        $this->command->info('Processing ' . count($geoJsonData['features']) . ' features...');

        // Buat direktori jika belum ada
        $destDir = 'public/geojson/kelurahan';
        if (!Storage::disk('public')->exists('geojson/kelurahan')) {
            Storage::disk('public')->makeDirectory('geojson/kelurahan');
        }

        $updated = 0;
        $notFound = 0;

        // Untuk setiap feature, match dengan kelurahan dan save file terpisah
        foreach ($geoJsonData['features'] as $feature) {
            if (!isset($feature['properties']['nm_kelurahan'])) {
                continue;
            }

            $namaKel = trim($feature['properties']['nm_kelurahan']);

            // Cari kelurahan di database
            $kelurahan = Kelurahan::where('nama', $namaKel)->first();

            if (!$kelurahan) {
                $this->command->warn("Kelurahan not found: $namaKel");
                $notFound++;
                continue;
            }

            // Buat single-feature GeoJSON
            $singleFeatureGeo = [
                'type' => 'FeatureCollection',
                'features' => [$feature]
            ];

            // Save ke file terpisah
            $filename = "geojson/kelurahan/{$kelurahan->id}.geojson";
            Storage::disk('public')->put($filename, json_encode($singleFeatureGeo, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Update database
            $kelurahan->update(['geojson_path' => $filename]);

            $updated++;
            $this->command->line("✓ {$namaKel} (ID: {$kelurahan->id})");
        }

        $this->command->info("Done! Updated: $updated, Not found: $notFound");
    }
}
