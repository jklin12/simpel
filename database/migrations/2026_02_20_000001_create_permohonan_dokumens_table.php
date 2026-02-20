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
        Schema::create('permohonan_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_surat_id')->constrained('permohonan_surats')->onDelete('cascade');
            $table->string('nama_dokumen');           // e.g. "Foto KTP", "Foto KK"
            $table->string('jenis_dokumen');           // e.g. "ktp", "kk", "surat_pengantar_rt", "foto_rumah"
            $table->string('file_path');               // storage path
            $table->string('original_name');           // original file name
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_dokumens');
    }
};
