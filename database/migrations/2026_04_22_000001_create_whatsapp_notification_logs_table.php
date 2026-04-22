<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permohonan_id')->nullable();
            $table->string('notification_type'); // 'created', 'approved', 'rejected', 'revisi', 'sign_request'
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->string('phone_to');
            $table->string('message_preview', 200)->nullable();
            $table->boolean('has_file')->default(false);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->unsignedSmallInteger('attempt')->default(1);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->foreign('permohonan_id')
                ->references('id')
                ->on('permohonan_surats')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_notification_logs');
    }
};
