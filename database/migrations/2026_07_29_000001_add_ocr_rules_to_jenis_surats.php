<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->json('ocr_rules')->nullable()->after('attachment_guides')
                ->comment('Aturan verifikasi OCR: daftar dokumen + instruksi per jenis surat');
        });
    }

    public function down(): void
    {
        Schema::table('jenis_surats', function (Blueprint $table) {
            $table->dropColumn('ocr_rules');
        });
    }
};
