<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanDokumen extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Daftar jenis dokumen yang dikenal sistem.
     */
    public const JENIS_DOKUMEN = [
        'ktp' => 'Foto KTP',
        'kk' => 'Foto KK',
        'surat_pengantar_rt' => 'Surat Pengantar RT',
        'foto_rumah' => 'Foto Rumah',
        'foto_usaha' => 'Foto Usaha',
        'akta_kelahiran' => 'Akta Kelahiran',
        'surat_pernyataan' => 'Surat Pernyataan',
        'surat_bidan' => 'Surat Bidan',
        'surat_rs' => 'Surat Rumah Sakit',
        'akta_pendirian' => 'Akta Pendirian',
        'ktp_alm' => 'KTP Almarhum',
        'ktp_ortu' => 'KTP Orang Tua',
        // SKTM
        'surat_pengantar_rtrw'      => 'Surat Pengantar RT/RW',
        'blangko_pernyataan'        => 'Blangko Pernyataan Bermeterai 10.000',
        'ktp_kk_bersangkutan'       => 'KTP & KK yang Bersangkutan',
        'ktp_saksi'                 => 'KTP 2 Orang Saksi',
        'surat_rekomendasi_sekolah' => 'Surat Pengantar/Rekomendasi Sekolah/Kampus',
        'bukti_lunas_pbb'           => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SKTMR
        'sktmr_surat_pengantar'    => 'Surat Pengantar RT/RW Setempat',
        'sktmr_blangko_pernyataan' => 'Blangko Pernyataan Bermeterai 10.000',
        'sktmr_ktp_kk'             => 'KTP & KK yang Bersangkutan',
        'sktmr_ktp_saksi'          => 'KTP 2 Orang Saksi (RT yang sama)',
        'sktmr_bukti_pbb'          => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // Generic
        'dokumen_lainnya' => 'Dokumen Lainnya',
    ];


    public function permohonanSurat(): BelongsTo
    {
        return $this->belongsTo(PermohonanSurat::class);
    }

    /**
     * Get label for jenis_dokumen.
     */
    public function getLabelAttribute(): string
    {
        return self::JENIS_DOKUMEN[$this->jenis_dokumen] ?? ucwords(str_replace('_', ' ', $this->jenis_dokumen));
    }
}
