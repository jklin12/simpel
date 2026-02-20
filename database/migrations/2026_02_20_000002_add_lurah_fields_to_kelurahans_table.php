<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->string('lurah_nama')->nullable()->after('kode');
            $table->string('lurah_nip')->nullable()->after('lurah_nama');
            $table->string('alamat')->nullable()->after('lurah_nip');
            $table->string('telp')->nullable()->after('alamat');
            // kop_surat_path: gambar kop surat lengkap (logo + teks instansi)
            // digunakan langsung sebagai header di PDF
            $table->string('kop_surat_path')->nullable()->after('telp');
        });
    }

    public function down(): void
    {
        Schema::table('m_kelurahans', function (Blueprint $table) {
            $table->dropColumn(['lurah_nama', 'lurah_nip', 'alamat', 'telp', 'kop_surat_path']);
        });
    }
};
