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
            $table->string('akronim', 20)->nullable()->after('kode');
            $table->string('lurah_pangkat', 100)->nullable()->after('lurah_nip');
            $table->string('lurah_golongan', 20)->nullable()->after('lurah_pangkat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->dropColumn(['akronim', 'lurah_pangkat', 'lurah_golongan']);
        });
    }
};
