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
        Schema::create('permohonan_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jenis_surat_id')->constrained('jenis_surats')->onDelete('cascade');
            $table->foreignId('kelurahan_id')->constrained('m_kelurahans')->onDelete('cascade');
            $table->foreignId('approval_flow_id')->nullable()->constrained('approval_flows')->onDelete('set null');
            
            // Data pemohon (tanpa login)
            $table->string('nama_pemohon');
            $table->string('nik_pemohon');
            $table->text('alamat_pemohon');
            $table->string('phone_pemohon')->nullable();
            
            // Data permohonan
            $table->json('data_permohonan')->nullable();
            $table->text('keperluan');
            $table->enum('status', ['draft', 'pending', 'in_review', 'approved', 'rejected', 'completed'])->default('draft');
            $table->integer('current_step')->nullable();
            
            // Data surat (setelah approved)
            $table->string('nomor_surat')->nullable()->unique();
            $table->date('tanggal_surat')->nullable();
            $table->string('file_path')->nullable();
            
            // Catatan
            $table->text('catatan')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_surats');
    }
};
