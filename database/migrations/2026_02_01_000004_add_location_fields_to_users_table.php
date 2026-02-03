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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('kelurahan_id')->nullable()->after('email')->constrained('m_kelurahans')->onDelete('set null');
            $table->foreignId('kecamatan_id')->nullable()->after('kelurahan_id')->constrained('m_kecamatans')->onDelete('set null');
            $table->foreignId('kabupaten_id')->nullable()->after('kecamatan_id')->constrained('m_kabupatens')->onDelete('set null');
            $table->string('phone')->nullable()->after('kabupaten_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kelurahan_id']);
            $table->dropForeign(['kecamatan_id']);
            $table->dropForeign(['kabupaten_id']);
            $table->dropColumn(['kelurahan_id', 'kecamatan_id', 'kabupaten_id', 'phone']);
        });
    }
};
