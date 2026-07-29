<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->string('ocr_status', 20)->default('not_configured')->after('rejected_reason')
                ->comment('not_configured|pending|verified|needs_review');
            $table->text('ai_insight')->nullable()->after('ocr_status')
                ->comment('Hasil verifikasi OCR dari AI (JSON)');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->dropColumn(['ocr_status', 'ai_insight']);
        });
    }
};
