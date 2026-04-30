<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('kode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });

        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('kode');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('geojson_path')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geojson_path']);
        });
    }
};
