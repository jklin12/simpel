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
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('telp');
            $table->enum('status_pejabat', ['Definitif', 'Plh', 'Plt'])->default('Definitif')->after('lurah_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'status_pejabat']);
        });
    }
};
