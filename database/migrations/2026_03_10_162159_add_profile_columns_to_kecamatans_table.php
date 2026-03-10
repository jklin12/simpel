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
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->string('kop_surat_path')->nullable()->after('kode');
            $table->string('camat_nama')->nullable()->after('kop_surat_path');
            $table->string('camat_nip')->nullable()->after('camat_nama');
            $table->string('camat_pangkat')->nullable()->after('camat_nip');
            $table->string('camat_golongan')->nullable()->after('camat_pangkat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->dropColumn([
                'kop_surat_path',
                'camat_nama',
                'camat_nip',
                'camat_pangkat',
                'camat_golongan'
            ]);
        });
    }
};
