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
        Schema::table('jenis_surats', function (Blueprint $table) {
            // JSON object: { "field_name": { "keterangan": "...", "contoh": "..." } }
            $table->text('attachment_guides')->nullable()->after('required_fields')
                ->comment('Petunjuk per field attachment untuk ditampilkan ke user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('attachment_guides');
        });
    }
};
