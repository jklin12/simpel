<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_revisi_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_surat_id')
                  ->constrained('permohonan_surats')
                  ->onDelete('cascade');
            $table->foreignId('requested_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->text('alasan');
            $table->foreignId('reviewed_by_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_reviewer')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedTinyInteger('revision_number')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_revisi_requests');
    }
};
