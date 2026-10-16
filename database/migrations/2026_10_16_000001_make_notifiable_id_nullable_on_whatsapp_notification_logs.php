<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Notifikasi on-demand (Notification::route, mis. WA permintaan TTD ke
     * Lurah/Camat) tidak punya model, sehingga notifiable_id harus boleh null.
     * Pakai SQL langsung karena doctrine/dbal tidak terpasang.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE whatsapp_notification_logs MODIFY notifiable_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::table('whatsapp_notification_logs')->whereNull('notifiable_id')->update(['notifiable_id' => 0]);
        DB::statement('ALTER TABLE whatsapp_notification_logs MODIFY notifiable_id BIGINT UNSIGNED NOT NULL');
    }
};
