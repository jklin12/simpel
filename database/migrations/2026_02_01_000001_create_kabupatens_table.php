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
        Schema::create('m_kabupatens', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kode')->unique()->nullable(); // Adjusted to nullable as JSON might not have code, or we derive it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_kabupatens');
    }
};
