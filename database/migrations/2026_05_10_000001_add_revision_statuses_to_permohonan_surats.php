<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE permohonan_surats MODIFY COLUMN status ENUM(
            'draft','pending','in_review','approved','rejected','completed',
            'revision_requested','revision_open'
        ) DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE permohonan_surats MODIFY COLUMN status ENUM(
            'draft','pending','in_review','approved','rejected','completed'
        ) DEFAULT 'draft'");
    }
};
