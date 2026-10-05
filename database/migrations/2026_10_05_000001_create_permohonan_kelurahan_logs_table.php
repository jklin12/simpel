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
        Schema::create('permohonan_kelurahan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_surat_id')->constrained('permohonan_surats')->onDelete('cascade');
            $table->foreignId('from_kelurahan_id')->constrained('m_kelurahans')->onDelete('cascade');
            $table->foreignId('to_kelurahan_id')->constrained('m_kelurahans')->onDelete('cascade');
            $table->foreignId('moved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('alasan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_kelurahan_logs');
    }
};
