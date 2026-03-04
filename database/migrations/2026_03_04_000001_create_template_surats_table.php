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
        Schema::create('template_surats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_surat_id')
                  ->constrained('jenis_surats')
                  ->cascadeOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path'); // path relatif di storage/app/public/template-surat/
            $table->string('file_original_name')->nullable(); // nama file asli untuk display
            $table->json('syarat')->nullable(); // array of string, syarat per template
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_surats');
    }
};
