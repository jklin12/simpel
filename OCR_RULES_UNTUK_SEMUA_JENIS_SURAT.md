# 📋 OCR RULES - Semua Jenis Surat

Panduan lengkap OCR Rules configuration untuk semua 17 jenis surat aktif.

---

## 📌 FORMAT OCR RULES

```json
{
  "dokumen": [
    {
      "jenis_dokumen": "field_name",
      "label": "Display Label",
      "wajib": true,
      "instruksi": "Verification instructions for AI"
    }
  ],
  "instruksi_global": "Cross-check instructions between documents"
}
```

---

## 1️⃣ SKTM - Surat Keterangan Tidak Mampu

**Status:** ✅ SUDAH ADA (Update terakhir: v1.0.9)

```json
{
  "dokumen": [
    {
      "jenis_dokumen": "ktp_kk_bersangkutan",
      "label": "KTP & KK yang Bersangkutan",
      "wajib": true,
      "instruksi": "Ekstrak data dari foto KTP dan KK. Bandingkan dengan data inputan: nama_lengkap, nik_bersangkutan, tempat_lahir, tanggal_lahir, jenis_kelamin, agama, alamat_lengkap, pekerjaan. Laporkan jika ada perbedaan."
    },
    {
      "jenis_dokumen": "surat_pengantar_rtrw",
      "label": "Surat Pengantar RT/RW",
      "wajib": true,
      "instruksi": "Baca surat pengantar RT/RW. Cari nama pemohon dan NIK, bandingkan dengan data inputan: nama_lengkap, nik_bersangkutan. Periksa juga nomor surat (no_surat_pengantar) dan RT/RW jika terbaca."
    },
    {
      "jenis_dokumen": "blangko_pernyataan",
      "label": "Blangko Pernyataan Bermeterai 10.000",
      "wajib": true,
      "instruksi": "Baca Blangko Pernyataan Bermeterai. Verifikasi data pemohon (nama, NIK, alamat) sesuai dengan inputan. Periksa tanggal surat pernyataan jika terbaca."
    },
    {
      "jenis_dokumen": "surat_rekomendasi_sekolah",
      "label": "Surat Pengantar/Rekomendasi Sekolah/Kampus",
      "wajib": true,
      "instruksi": "Periksa surat rekomendasi sekolah/kampus. Cari nama pemohon dan bandingkan dengan nama_lengkap. Jika keperluan_sktm terkait pendidikan, pastikan surat ini sesuai."
    },
    {
      "jenis_dokumen": "ktp_saksi",
      "label": "KTP 2 Orang Saksi (RT yang sama)",
      "wajib": true,
      "instruksi": "Periksa apakah ada 2 KTP SAKSI yang berbeda (nama dan NIK berbeda). SAKSI TIDAK PERLU COCOK dengan data pemohon. Cukup pastikan ada 2 identitas berbeda."
    },
    {
      "jenis_dokumen": "bukti_lunas_pbb",
      "label": "Bukti Tanda Lunas PBB-P2 Tahun Berjalan",
      "wajib": false,
      "instruksi": "Baca dokumen bukti lunas PBB. Cari Nama/NIK pemohon di dokumen jika terbaca. Periksa tahun berjalan dan alamat objek PBB jika tersedia."
    }
  ],
  "instruksi_global": "Cross-check konsistensi data pribadi (nama, NIK, alamat) di SEMUA dokumen yang bisa dibaca. Pastikan tidak ada kontradiksi antar dokumen."
}
```

---

## 2️⃣ SKTMR - Surat Keterangan Belum Memiliki Rumah

**Status:** ⏳ BELUM ADA (Ready to implement)

```json
{
  "dokumen": [
    {
      "jenis_dokumen": "sktmr_surat_pengantar",
      "label": "Surat Pengantar RT/RW",
      "wajib": true,
      "instruksi": "Baca surat pengantar. Cari nama, NIK, dan RT/RW. Bandingkan dengan inputan: nama_lengkap, nik_bersangkutan, rt, rw."
    },
    {
      "jenis_dokumen": "sktmr_blangko_pernyataan",
      "label": "Blangko Pernyataan Bermeterai",
      "wajib": true,
      "instruksi": "Verifikasi data pemohon di blangko pernyataan sesuai inputan."
    },
    {
      "jenis_dokumen": "sktmr_ktp_kk",
      "label": "KTP & KK",
      "wajib": true,
      "instruksi": "Ekstrak dan bandingkan data: nama, NIK, alamat, tempat lahir, tanggal lahir, jenis kelamin, agama dengan inputan."
    },
    {
      "jenis_dokumen": "sktmr_ktp_saksi",
      "label": "KTP 2 Saksi",
      "wajib": true,
      "instruksi": "Pastikan ada 2 identitas berbeda (nama dan NIK berbeda)."
    },
    {
      "jenis_dokumen": "sktmr_bukti_pbb",
      "label": "Bukti Lunas PBB",
      "wajib": false,
      "instruksi": "Verifikasi bukti lunas PBB tahun berjalan jika tersedia."
    }
  ],
  "instruksi_global": "Cross-check data pribadi konsisten di semua dokumen."
}
```

---

## 3️⃣ SKP - Surat Keterangan Penghasilan

**Status:** ⏳ BELUM ADA

```json
{
  "dokumen": [
    {
      "jenis_dokumen": "skp_surat_pengantar",
      "label": "Surat Pengantar RT/RW",
      "wajib": true,
      "instruksi": "Baca surat pengantar, ekstrak nama, NIK, RT/RW."
    },
    {
      "jenis_dokumen": "skp_blangko_pernyataan",
      "label": "Blangko Pernyataan",
      "wajib": true,
      "instruksi": "Verifikasi data pemohon dan informasi penghasilan jika ada."
    },
    {
      "jenis_dokumen": "skp_ktp_kk",
      "label": "KTP & KK",
      "wajib": true,
      "instruksi": "Ekstrak data identitas dan bandingkan dengan inputan."
    },
    {
      "jenis_dokumen": "skp_ktp_saksi",
      "label": "KTP Saksi",
      "wajib": true,
      "instruksi": "Verifikasi 2 identitas saksi yang berbeda."
    }
  ],
  "instruksi_global": "Konsistensi data pribadi di semua dokumen."
}
```

---

## TEMPLATE UNTUK JENIS SURAT LAINNYA

Gunakan template di bawah untuk SKBM, SKM, SPN, SDNH, SKJD, SKSI, SKG, SPKH, SKB, SKDKO, SPRIK, SKDK, ROIPK, SPKDK:

```json
{
  "dokumen": [
    {
      "jenis_dokumen": "field_name",
      "label": "Display Label",
      "wajib": true/false,
      "instruksi": "Verification instructions tailored to document purpose"
    }
  ],
  "instruksi_global": "Cross-check instructions between documents if multiple docs"
}
```

---

## 📝 DOKUMEN FIELDS PER JENIS SURAT

### SKBM (Surat Keterangan Belum Menikah)
- skbm_surat_pengantar (required)
- skbm_blangko_pernyataan (required)
- skbm_ktp_kk (required)
- skbm_ktp_saksi (required)

### SKM (Surat Keterangan Kematian)
- skm_surat_pengantar (required)
- skm_blangko_pernyataan (required)
- skm_ktp_kk_pemohon (required)
- skm_ktp_kk_meninggal (required)
- skm_ktp_saksi (required)

### SPN (Surat Pengantar Nikah)
- skmh_surat_pengantar (required)
- skmh_akta_ijazah_catin (required)
- skmh_ktp_kk_catin (required)
- skmh_akta_cerai_kematian (optional)
- skmh_dispensasi_pengadilan (optional)
- skmh_izin_atasan (optional)
- skmh_izin_poligami (optional)
- skmh_rekom_dp3a (optional)
- skmh_surat_imunisasi_catin (optional)

### SDNH (Surat Dispensasi Nikah)
- sdnh_akta_cerai_mati (required)
- [Other fields as per form]

### SKJD (Surat Keterangan Janda/Duda)
- skjd_surat_pengantar_rtrw (required)
- skjd_blangko_pernyataan (required)
- skjd_ktp_kk_bersangkutan (required)
- skjd_ktp_saksi (required)
- skjd_bukti_lunas_pbb (required)

---

## 🚀 IMPLEMENTATION STEPS

1. **Create Migration** untuk update jenis_surats table dengan ocr_rules
2. **Generate OCR rules** untuk setiap jenis surat
3. **Seed database** dengan rules
4. **Test dengan sample dokumen** untuk setiap jenis surat
5. **Document instruksi** per jenis surat

---

## ✅ CHECKLIST SEBELUM DEPLOY

- [ ] OCR rules defined untuk semua jenis surat
- [ ] Instruksi verifikasi clear dan specific
- [ ] Dokumen wajib vs optional sudah correct
- [ ] Cross-check instructions relevan
- [ ] Tested dengan sample dokumen
- [ ] Database updated
- [ ] UI accessible untuk edit rules

---

## 🐛 KNOWN ISSUES & FIXES (v1.0.9+)

### Issue #1: Tanggal Validasi
- **Problem**: Tanggal dokumen bisa di masa depan dari hari submit
- **Fix**: Instruksi updated - "tanggal tidak di masa depan dari hari ini"  
- **Logic**: Tanggal surat harus ≤ tanggal submit form
- **Example**: Data 1296: Tanggal surat pernyataan 2026-07-29 ditolak (valid, sudah ada)

### Issue #2: KTP Saksi Validation
- **Problem**: AI membandingkan KTP saksi dengan data pemohon (salah logic)
- **Fix**: Instruksi updated - "JANGAN bandingkan dengan pemohon, hanya verify 2 saksi berbeda"
- **Logic**: Verifikasi 2 KTP saksi hanya butuh berbeda satu sama lain, bukan match form data
- **Example**: Data 1296: 2 KTP saksi berbeda (DARUSMAN, SITI ARIFAH) OK walaupun tidak match pemohon (M. ARIE SHANDY)

---

## 📌 NOTES

- Instruksi harus **specific ke dokumen** dan **contextual ke form data**
- Hindari instruksi generic yang tidak membantu AI verify
- Test dengan real dokumen sebelum go-live
- Update instruksi berdasarkan verification accuracy feedback
- Seeder: `database/seeders/SetupOcrRulesSeeder.php` contains all updated instructions with bug fixes

---

**Document Version:** 1.1  
**Last Updated:** 2026-07-31 (Fixed OCR validation logic bugs)  
**Ready for:** Database deployment & testing
