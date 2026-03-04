<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM menjadi VARCHAR agar bisa menampung 'fasilitas_umum' baru
        // Menghindari butuhnya package doctrine/dbal dengan raw query
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE portal_data_kelurahans MODIFY COLUMN kategori VARCHAR(255) NOT NULL");

        Schema::table('portal_data_kelurahans', function (Blueprint $table) {
            $table->string('jenis_fasilitas')->nullable()->after('kategori');
            $table->string('status_fasilitas')->nullable()->after('jenis_fasilitas');
            $table->string('rt')->nullable()->after('status_fasilitas');
            $table->string('rw')->nullable()->after('rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portal_data_kelurahans', function (Blueprint $table) {
            $table->dropColumn(['jenis_fasilitas', 'status_fasilitas', 'rt', 'rw']);
        });

        // Kembalikan ke ENUM
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE portal_data_kelurahans MODIFY COLUMN kategori ENUM('rw', 'rt', 'lpm', 'tempat_ibadah', 'pemakaman', 'sarana_pendidikan', 'fasilitas_kesehatan', 'fasilitas_keamanan', 'pos_kamling') NOT NULL");
    }
};
