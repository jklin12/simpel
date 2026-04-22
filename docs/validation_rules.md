# Validasi Permohonan Surat — Dokumentasi Lengkap

Dokumentasi ini mencakup semua aturan validasi untuk setiap jenis surat yang tersedia di sistem Simpel Landasan Ulin.

---

## 📋 Daftar Isi

1. [SKM — Surat Keterangan Kematian](#skm--surat-keterangan-kematian)
2. [SKTM — Surat Keterangan Tidak Mampu](#sktm--surat-keterangan-tidak-mampu)
3. [SKTMR — Surat Keterangan Tidak Memiliki Rumah](#sktmr--surat-keterangan-tidak-memiliki-rumah)
4. [SKBM — Surat Keterangan Belum Menikah](#skbm--surat-keterangan-belum-menikah)
5. [SKP — Surat Keterangan Penghasilan](#skp--surat-keterangan-penghasilan)
6. [SKJD — Surat Keterangan Janda/Duda](#skjd--surat-keterangan-jandaduda)
7. [SKSI — Surat Keterangan Suami Istri](#sksi--surat-keterangan-suami-istri)
8. [SKG — Surat Keterangan Gaib](#skg--surat-keterangan-gaib)
9. [SPN / SKMH — Surat Permohonan Nikah](#spn--skmh--surat-permohonan-nikah)
10. [SDNH — Surat Dispensasi Nikah](#sdnh--surat-dispensasi-nikah)

---

## SKM — Surat Keterangan Kematian

### Surat Pengantar
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nomor_pengantar` | text | required, max:50 | Nomor surat pengantar RT/RW |
| `tanggal_pengantar` | date | required, date | Tanggal dikeluarkan surat pengantar |
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |

### Data Jenazah
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_jenazah` | text | required, max:255 | Nama lengkap yang meninggal |
| `nik_jenazah` | text | required, size:16 | NIK harus tepat 16 karakter |
| `jk_jenazah` | select | required, in:L,P | Laki-laki (L) atau Perempuan (P) |
| `tempat_lahir_jenazah` | text | required, max:100 | Tempat lahir jenazah |
| `tanggal_lahir_jenazah` | date | required, date | Tanggal lahir jenazah |
| `alamat_jenazah` | textarea | required | Alamat sesuai identitas |
| `agama_jenazah` | select | required | Islam, Kristen, Katolik, Hindu, Buddha, Konghucu |
| `pekerjaan_jenazah` | text | required, max:100 | Pekerjaan terakhir |

### Detail Kematian
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `hari_meninggal` | select | required | Hari dalam seminggu |
| `tanggal_meninggal` | date | required, date | Tanggal meninggal |
| `pukul_meninggal` | text | required | Waktu meninggal (HH:MM) |
| `tempat_meninggal` | text | required, max:255 | Tempat meninggal |
| `sebab_kematian` | text | required, max:255 | Sebab/penyebab kematian |
| `tempat_pemakaman` | text | required, max:255 | Lokasi pemakaman |

### Data Pelapor
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_pelapor` | text | required, max:255 | Nama pelapor |
| `nik_pelapor` | text | required, size:16 | NIK pelapor (16 digit) |
| `alamat_pelapor` | text | nullable | Alamat pelapor (opsional) |
| `hubungan_pelapor` | text | required, max:100 | Hubungan dengan jenazah |
| `no_wa` | text | required, max:20 | Nomor WhatsApp pelapor |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skm_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skm_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skm_ktp_kk_pemohon` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skm_ktp_kk_meninggal` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skm_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (1 file untuk 2 saksi) |
| `skm_bukti_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |

---

## SKTM — Surat Keterangan Tidak Mampu

### Data Diri
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap pemohon |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Islam, Kristen, Katolik, Hindu, Buddha, Konghucu |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required | Belum Kawin, Kawin, Cerai Hidup, Cerai Mati |
| `pekerjaan` | select | required, max:100 | Pilihan dari master pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |
| `keperluan_sktm` | text | required | Keperluan surat SKTM |
| `keterangan_sktm` | text | required | Keterangan tambahan |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `surat_pengantar_rtrw` | file | required, max:5120 KB | JPG, PNG, PDF |
| `blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF (bermeterai Rp 10.000) |
| `ktp_kk_bersangkutan` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `surat_rekomendasi_sekolah` | file | required, max:5120 KB | JPG, PNG, PDF (jika ada) |
| `bukti_lunas_pbb` | file | required, max:5120 KB | JPG, PNG, PDF (tahun berjalan) |

---

## SKTMR — Surat Keterangan Tidak Memiliki Rumah

### Data Diri
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required | Status perkawinan |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `pendidikan_terakhir` | select | required | Pendidikan terakhir |
| `alamat_lengkap` | textarea | required | Alamat tinggal saat ini |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |
| `keperluan` | text | required, max:255 | Keperluan surat |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `sktmr_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `sktmr_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `sktmr_ktp_kk` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `sktmr_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `sktmr_bukti_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |

---

## SKBM — Surat Keterangan Belum Menikah

### Data Diri
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required | Status perkawinan |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `keperluan` | text | required, max:255 | Keperluan surat |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skbm_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skbm_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skbm_ktp_kk` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skbm_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `skbm_bukti_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |

---

## SKP — Surat Keterangan Penghasilan

### Data Diri
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required, in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati | - |
| `pekerjaan` | text | required | Pekerjaan |
| `pendidikan_terakhir` | text | required | Pendidikan terakhir |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |
| `jumlah_penghasilan` | text | required | Jumlah penghasilan (bisa format ribuan) |
| `keperluan` | text | required | Keperluan surat |

### Surat Pengantar
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required | Nomor RT |
| `rw` | text | required | Nomor RW |
| `no_surat_pengantar` | text | required | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skp_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skp_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skp_ktp_kk` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skp_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `skp_bukti_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |

---

## SKJD — Surat Keterangan Janda/Duda

### Data Diri
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required, in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati | - |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `keperluan` | text | required, max:255 | Keperluan surat |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skjd_surat_pengantar_rtrw` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skjd_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skjd_ktp_kk_bersangkutan` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skjd_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `skjd_bukti_lunas_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skjd_akta_kematian_perceraian` | file | required, max:5120 KB | JPG, PNG, PDF (Akta Kematian atau Akta Perceraian) |

---

## SKSI — Surat Keterangan Suami Istri

### Data Diri Pemohon
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |

### Data Istri/Pasangan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `istri_nama` | text | required, max:255 | Nama lengkap istri/pasangan |
| `istri_nik` | text | required, size:16 | NIK istri/pasangan (16 digit) |
| `istri_jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `istri_agama` | select | required | Pilihan agama |
| `istri_tempat_lahir` | text | required, max:100 | Tempat lahir |
| `istri_tanggal_lahir` | date | required, date | Tanggal lahir |
| `istri_pekerjaan` | select | required, max:100 | Pekerjaan |
| `istri_alamat` | textarea | required | Alamat sesuai KTP |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `sksi_surat_pengantar_rtrw` | file | required, max:5120 KB | JPG, PNG, PDF |
| `sksi_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF (bermeterai Rp 10.000) |
| `sksi_ktp_kk_bersangkutan` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `sksi_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `sksi_bukti_lunas_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |
| `sksi_surat_pernyataan_penikah` | file | required, max:5120 KB | JPG, PNG, PDF (KTP yang menikahkan & 2 saksi, 1 file) |

---

## SKG — Surat Keterangan Gaib

### Data Diri Pemohon
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `keperluan` | text | required, max:255 | Keperluan surat |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |

### Data Orang Gaib
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `gaib_nama` | text | required, max:255 | Nama orang gaib |
| `gaib_nik` | text | required, size:16 | NIK orang gaib (16 digit) |
| `gaib_jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `gaib_agama` | select | required | Pilihan agama |
| `gaib_tempat_lahir` | text | required, max:100 | Tempat lahir |
| `gaib_tanggal_lahir` | date | required, date | Tanggal lahir |
| `gaib_pekerjaan` | select | required, max:100 | Pekerjaan |
| `gaib_alamat` | textarea | required | Alamat sesuai KTP |

### Surat Pengantar RT/RW
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `rt` | text | required, max:10 | Nomor RT |
| `rw` | text | required, max:10 | Nomor RW |
| `no_surat_pengantar` | text | required, max:100 | Nomor surat pengantar |
| `tanggal_surat_pengantar` | date | required, date | Tanggal surat pengantar |

### Surat Pernyataan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `tanggal_surat_pernyataan` | date | required, date | Tanggal penandatanganan |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skg_surat_pengantar_rtrw` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skg_blangko_pernyataan` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skg_ktp_kk_bersangkutan` | file | required, max:5120 KB | JPG, PNG, PDF (1 file) |
| `skg_ktp_saksi` | file | required, max:5120 KB | JPG, PNG, PDF (2 saksi, 1 file) |
| `skg_bukti_lunas_pbb` | file | required, max:5120 KB | JPG, PNG, PDF |

---

## SPN / SKMH — Surat Permohonan Nikah

### Data Diri Pemohon
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap pemohon |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `jenis_kelamin` | select | required, in:Laki-laki,Perempuan | - |
| `agama` | select | required | Pilihan agama |
| `kewarganegaraan` | select | required, in:WNI,WNA | - |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `status_perkawinan` | select | required | Jejaka, Duda, Beristri ke-2/3/4, Perawan, Janda |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |
| `no_wa` | text | required, max:20 | Nomor WhatsApp (harus WA) |

### Data Orang Tua — Ayah
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `ayah_nama` | text | required, max:255 | Nama ayah |
| `ayah_bin` | text | required, max:255 | Bin (nama kakek dari ayah) |
| `ayah_nik` | text | required, size:16 | NIK ayah (16 digit) |
| `ayah_agama` | select | required | Pilihan agama |
| `ayah_kewarganegaraan` | select | required, in:WNI,WNA | - |
| `ayah_pekerjaan` | select | required | Pekerjaan ayah |
| `ayah_tempat_lahir` | text | required, max:100 | Tempat lahir ayah |
| `ayah_tanggal_lahir` | date | required, date | Tanggal lahir ayah |
| `ayah_alamat` | textarea | required | Alamat ayah sesuai KTP |

### Data Orang Tua — Ibu
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `ibu_nama` | text | required, max:255 | Nama ibu |
| `ibu_binti` | text | required, max:255 | Binti (nama kakek dari ibu) |
| `ibu_nik` | text | required, size:16 | NIK ibu (16 digit) |
| `ibu_agama` | select | required | Pilihan agama |
| `ibu_kewarganegaraan` | select | required, in:WNI,WNA | - |
| `ibu_pekerjaan` | select | required | Pekerjaan ibu |
| `ibu_tempat_lahir` | text | required, max:100 | Tempat lahir ibu |
| `ibu_tanggal_lahir` | date | required, date | Tanggal lahir ibu |
| `ibu_alamat` | textarea | required | Alamat ibu sesuai KTP |

### Dokumen Lampiran — WAJIB
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skmh_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `skmh_akta_ijazah_catin` | file | required, max:5120 KB | Akta Kelahiran & Ijazah Terakhir (1 file) |
| `skmh_ktp_kk_catin` | file | required, max:5120 KB | KTP & KK kedua calon pengantin (1 file) |
| `skmh_ktp_kk_ortu` | file | required, max:5120 KB | KTP & KK orang tua/wali (1 file) |
| `skmh_pas_foto` | file | required, max:5120 KB | Foto warna gandeng (latar biru) |
| `skmh_ktp_saksi` | file | required, max:5120 KB | KTP 2 saksi RT yang sama (1 file) |
| `skmh_form_n2_n5` | file | required, max:5120 KB | Formulir Pengantar Nikah N2-N5 |
| `skmh_bukti_pbb` | file | required, max:5120 KB | Bukti Tanda Lunas PBB-P2 |

### Dokumen Lampiran — OPSIONAL (Jika Diperlukan)
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `skmh_akta_cerai_kematian` | file | nullable, max:5120 KB | Akta Cerai/Kematian pasangan sebelumnya (jika duda/janda) |
| `skmh_dispensasi_pengadilan` | file | nullable, max:5120 KB | Surat dispensasi dari pengadilan (jika belum 19 tahun) |
| `skmh_izin_atasan` | file | nullable, max:5120 KB | Surat izin atasan/kesatuan (jika TNI/POLRI) |
| `skmh_izin_poligami` | file | nullable, max:5120 KB | Penetapan izin poligami dari pengadilan (jika hendak poligami) |
| `skmh_rekom_dp3a` | file | nullable, max:5120 KB | Surat rekomendasi DP3APMP2KB (jika di bawah usia menikah) |
| `skmh_surat_imunisasi_catin` | file | nullable, max:5120 KB | Surat imunisasi calon pengantin |

---

## SDNH — Surat Dispensasi Nikah

### Data Pemohon
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_lengkap` | text | required, max:255 | Nama lengkap |
| `nik_bersangkutan` | text | required, size:16 | NIK (16 digit) |
| `tempat_lahir` | text | required, max:100 | Tempat lahir |
| `tanggal_lahir` | date | required, date | Tanggal lahir |
| `jenis_kelamin` | select | required, in:LAKI-LAKI,PEREMPUAN | - |
| `agama` | select | required | Pilihan agama |
| `status_perkawinan` | select | required | Status perkawinan |
| `pekerjaan` | select | required, max:100 | Pekerjaan |
| `no_wa` | text | required, max:20 | Nomor WhatsApp |
| `alamat_lengkap` | textarea | required | Alamat sesuai KTP |

### Data Pasangan
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `nama_pasangan` | text | required, max:255 | Nama lengkap pasangan |
| `tempat_lahir_pasangan` | text | required, max:100 | Tempat lahir pasangan |
| `tanggal_lahir_pasangan` | date | required, date | Tanggal lahir pasangan |
| `jenis_kelamin_pasangan` | select | required, in:LAKI-LAKI,PEREMPUAN | - |
| `agama_pasangan` | select | required | Pilihan agama pasangan |
| `status_perkawinan_pasangan` | select | required | Status perkawinan pasangan |
| `pekerjaan_pasangan` | select | required, max:100 | Pekerjaan pasangan |
| `alamat_pasangan` | textarea | required | Alamat pasangan sesuai KTP |

### Pelaksanaan (Pernikahan Berencana)
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `hari_pernikahan` | select | required | Hari dalam seminggu |
| `tanggal_pernikahan` | date | required, date | Tanggal rencana pernikahan |
| `pukul_pernikahan` | text | required | Waktu rencana pernikahan (HH:MM) |
| `alamat_pernikahan` | textarea | required | Lokasi pelaksanaan pernikahan |
| `alasan_dispensasi` | textarea | required | Alasan perlu dispensasi (usia, kesehatan, dll) |

### Dokumen Lampiran
| Field | Tipe | Validasi | Keterangan |
|-------|------|----------|-----------|
| `sdnh_surat_pengantar` | file | required, max:5120 KB | JPG, PNG, PDF |
| `sdnh_ktp_kk` | file | required, max:5120 KB | KTP & KK calon pengantin (1 file) |
| `sdnh_formulir_n` | file | required, max:5120 KB | Formulir N (permohonan dispensasi) |
| `sdnh_lunas_pbb` | file | required, max:5120 KB | Bukti Lunas PBB-P2 |
| `sdnh_akta_cerai_mati` | file | nullable, max:5120 KB | Akta cerai/kematian (jika berlaku) |

---

## 📝 Catatan Umum

### Validasi Umum untuk Semua Jenis Surat
- **jenis_surat_id**: required, harus ada di tabel jenis_surats
- **kelurahan_id**: required, harus ada di tabel m_kelurahans

### Aturan File Upload
- **Format**: JPG, JPEG, PNG, PDF
- **Ukuran Maksimal**: 5 MB (5120 KB)
- **Beberapa dokumen harus dijadikan 1 file**:
  - KTP & KK bersangkutan → 1 file
  - KTP 2 Orang Saksi → 1 file (digabung)
  - Dokumen yang disebutkan "(Dijadikan 1 File)" harus dalam satu file PDF

### Aturan Tanggal
- Semua field tanggal harus valid (format: YYYY-MM-DD)
- Tanggal tidak boleh di masa depan (umumnya)
- Format waktu untuk jam: HH:MM

### Aturan Text & Number
- **NIK**: Harus tepat 16 digit, tidak boleh kurang atau lebih
- **Nomor HP**: Max 20 karakter (termasuk kode negara jika ada)
- **Nama & Alamat**: Max sesuai yang ditentukan per field
- **String umum**: Maksimal 1000 karakter (untuk teks panjang)

### Dynamic Fields
Untuk jenis surat yang tidak ada di list ini (menggunakan sistem dynamic), validasi mengikuti definisi di `jenis_surats.required_fields` JSON.

---

**Last Updated**: 2026-04-22  
**Version**: 1.0
