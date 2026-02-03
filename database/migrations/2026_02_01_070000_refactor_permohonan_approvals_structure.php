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
        Schema::table('permohonan_approvals', function (Blueprint $table) {
            // Drop foreign key and column for approval_step_id
            // Note: constraint name usually table_column_foreign
            $table->dropForeign(['approval_step_id']);
            $table->dropColumn('approval_step_id');

            // Add new columns to track flow manually
            $table->string('target_role')->after('permohonan_surat_id')->nullable();
            $table->string('step_name')->after('target_role')->nullable(); // e.g., "Verifikasi Kelurahan"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_approvals', function (Blueprint $table) {
            $table->foreignId('approval_step_id')->constrained('approval_steps')->onDelete('cascade');
            $table->dropColumn(['target_role', 'step_name']);
        });
    }
};
