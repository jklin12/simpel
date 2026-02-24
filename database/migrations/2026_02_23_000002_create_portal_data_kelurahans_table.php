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
        Schema::create('portal_data_kelurahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelurahan_id')->constrained('m_kelurahans')->onDelete('cascade');
            $table->enum('kategori', [
                'rw',
                'rt',
                'lpm',
                'tempat_ibadah',
                'pemakaman',
                'sarana_pendidikan',
                'fasilitas_kesehatan',
                'fasilitas_keamanan',
                'pos_kamling',
            ]);
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->string('alamat')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('foto')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_data_kelurahans');
    }
};
