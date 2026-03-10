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
            $table->string('lurah_no_hp')->nullable()->after('lurah_nip');
        });

        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->string('camat_no_hp')->nullable()->after('camat_nip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->dropColumn('lurah_no_hp');
        });

        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->dropColumn('camat_no_hp');
        });
    }
};
