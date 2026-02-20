<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL doesn't support directly modifying ENUMs easily, so we use raw SQL
        DB::statement("ALTER TABLE permohonan_surats MODIFY COLUMN status ENUM('draft','pending','in_review','approved','rejected','completed') DEFAULT 'draft'");

        Schema::table('permohonan_surats', function (Blueprint $table) {
            // Path to the signed PDF uploaded by admin after digital signature
            $table->string('signed_file_path')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->dropColumn('signed_file_path');
        });

        DB::statement("ALTER TABLE permohonan_surats MODIFY COLUMN status ENUM('draft','pending','in_review','rejected','completed') DEFAULT 'draft'");
    }
};
