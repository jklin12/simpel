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
        // SKBM
        'skbm_surat_pengantar'     => 'Surat Pengantar RT/RW Setempat',
        'skbm_blangko_pernyataan'  => 'Blangko Pernyataan Bermeterai 10.000',
        'skbm_ktp_kk'              => 'KTP & KK yang Bersangkutan',
        'skbm_ktp_saksi'           => 'KTP 2 Orang Saksi (RT yang sama)',
        'skbm_bukti_pbb'           => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SKP
        'skp_surat_pengantar'     => 'Surat Pengantar RT/RW Setempat',
        'skp_blangko_pernyataan'  => 'Blangko Pernyataan Bermeterai 10.000',
        'skp_ktp_kk'              => 'KTP & KK yang Bersangkutan',
        'skp_ktp_saksi'           => 'KTP 2 Orang Saksi (RT yang sama)',
        'skp_bukti_pbb'           => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SPKH
        'spkh_surat_pengantar_rtrw' => 'Surat Pengantar RT/RW Setempat',
        'spkh_ktp_kk_pemohon'      => 'KTP & KK Pemohon (Dijadikan 1 File)',
        'spkh_surat_pernyataan'    => 'Surat Pernyataan Pemohon',
        'spkh_ktp_saksi'           => 'KTP 2 Orang Saksi (Dijadikan 1 File)',
        'spkh_bukti_pbb'           => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SKB
        'skb_surat_pengantar_rtrw' => 'Surat Pengantar RT/RW Setempat',
        'skb_ktp_kk_pemohon'       => 'KTP & KK Pemohon (Dijadikan 1 File)',
        'skb_bukti_pbb'            => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SKDKO
        'skdko_surat_pengantar_rtrw'    => 'Surat Pengantar RT/RW Setempat',
        'skdko_ktp_kk_pemohon'          => 'KTP & KK Pemohon (Dijadikan 1 File)',
        'skdko_sk_kepengurusan'         => 'Struktur Organisasi/SK Kepengurusan',
        'skdko_bukti_pbb'               => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        'skdko_akta_pendirian'          => 'Akta Pendirian',
        'skdko_npwp'                    => 'NPWP',
        'skdko_surat_pernyataan_warga'  => 'Surat Pernyataan dari Lingkungan Sekitar (Minimal 20 Orang)',
        // SPRIK
        'sprik_surat_pengantar_rtrw' => 'Surat Pengantar RT/RW Setempat',
        'sprik_ktp_kk_pemohon'       => 'KTP & KK Pemohon (Dijadikan 1 File)',
        'sprik_ktp_penyelenggara'    => 'KTP Penyelenggara/Pelatih/Pembimbing Kursus & Pelatihan (Dijadikan 1 File)',
        'sprik_bukti_pbb'            => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SKDK
        'skdk_surat_pengantar_rtrw'  => 'Surat Pengantar RT/RW Setempat',
        'skdk_ktp_pemohon'           => 'KTP Pemohon',
        'skdk_akta_kepengurusan'     => 'Akta Pendirian / Kepengurusan',
        'skdk_akta_parpol'           => 'Akta Pendirian Partai Politik',
        'skdk_imb_sewa'              => 'IMB / Perjanjian Sewa Bangunan',
        'skdk_surat_pengantar_lurah' => 'Surat Pengantar Domisili Kepartaian dari Kelurahan',
        'skdk_bukti_pbb'             => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // ROIPK
        'roipk_surat_pengantar_rtrw'  => 'Surat Pengantar RT/RW Setempat',
        'roipk_ktp_kk_pemohon'        => 'KTP dan KK Pemohon',
        'roipk_surat_permohonan'      => 'Surat Permohonan Izin Penyelenggaraan Kursus',
        'roipk_struktur_organisasi'   => 'Struktur Organisasi',
        'roipk_ijazah_kompetensi'     => 'Ijazah Kompetensi Penyelenggara dan Tenaga Pengajar',
        'roipk_izin_tetangga'         => 'Izin Tetangga (Diketahui Ketua RT + Fotokopi KTP)',
        'roipk_daftar_fasilitas'      => 'Daftar Fasilitas Kelengkapan Belajar dan Warga Belajar',
        'roipk_silabus'               => 'Daftar Silabus Pembelajaran',
        'roipk_surat_pengantar_lurah' => 'Surat Pengantar Rekomendasi Izin Penyelenggaraan Kursus dari Kelurahan',
        'roipk_bukti_pbb'             => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
        // SPKDK
        'spkdk_surat_pengantar_rtrw' => 'Surat Pengantar RT/RW Setempat',
        'spkdk_ktp_kk_pemohon'       => 'KTP dan KK Pemohon',
        'spkdk_struktur_organisasi'  => 'Struktur Organisasi/SK Kepengurusan',
        'spkdk_bukti_pbb'            => 'Bukti Tanda Lunas PBB-P2 Tahun Berjalan',
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
