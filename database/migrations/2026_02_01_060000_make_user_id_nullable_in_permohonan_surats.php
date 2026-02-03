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
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable()->change();
            // Drop foreign key first if needed depending on DB, but usually change() works if defined correctly.
            // If strict mode on SQLite/MySQL might need dropping constraint. 
            // For now assuming standard MySQL.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by_user_id')->nullable(false)->change();
        });
    }
};
