<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->enum('status_pejabat', ['Definitif', 'Plh', 'Plt'])->default('Definitif')->after('camat_golongan');
        });
    }

    public function down(): void
    {
        Schema::table('m_kecamatans', function (Blueprint $table) {
            $table->dropColumn('status_pejabat');
        });
    }
};
