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
        Schema::create('portal_struktur_organisasis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('foto')->nullable();

            // Relasi ke tabel yang sama (Self-referencing foreign key)
            // nullable karena tingkat tertinggi (Camat) tidak punya parent
            $table->foreignId('parent_id')->nullable()->constrained('portal_struktur_organisasis')->nullOnDelete();

            // Kolom untuk mengurutkan posisi jika dalam satu parent yang sama
            $table->integer('urutan')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_struktur_organisasis');
    }
};
