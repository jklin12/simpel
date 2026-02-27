<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_sliders', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('sub_judul')->nullable();
            $table->string('gambar')->nullable();
            $table->string('warna_tema')->default('blue'); // blue | green | orange
            $table->string('label_cta_1')->nullable();
            $table->string('url_cta_1')->nullable();
            $table->string('label_cta_2')->nullable();
            $table->string('url_cta_2')->nullable();
            $table->integer('urutan')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_sliders');
    }
};
