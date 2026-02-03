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
        Schema::create('surat_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->onDelete('cascade');
            $table->foreignId('kelurahan_id')->constrained('m_kelurahans')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('counter')->default(0);
            $table->timestamps();

            // Unique constraint untuk kombinasi jenis_surat, kelurahan, tahun, dan bulan
            $table->unique(['jenis_surat_id', 'kelurahan_id', 'tahun', 'bulan'], 'surat_counter_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_counters');
    }
};
