<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Rekomendasi Operasional Izin Penyelenggaraan Kursus</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
        }

        .page {
            padding: 1cm 2cm 1.5cm 2cm;
        }

        /* HEADER */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px solid #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header-text-line1 { font-size: 11pt; font-weight: normal; }
        .header-text-line2 { font-size: 18pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; line-height: 1.1; }
        .header-text-line4 { font-size: 8pt; margin-top: 2px; }

        /* BLOK SURAT */
        .tanggal-kanan {
            text-align: right;
            font-size: 10pt;
            margin-bottom: 8px;
        }

        table.blok-surat {
            width: 70%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        table.blok-surat td {
            font-size: 10pt;
            vertical-align: top;
            padding: 1px 0;
        }

        table.blok-surat td.lbl { width: 22%; }
        table.blok-surat td.sep { width: 4%; }
        table.blok-surat td.val { width: 74%; }

        /* KEPADA YTH */
        .kepada { font-size: 10pt; margin-bottom: 12px; }
        .kepada-nama { font-size: 10pt; }
        .kepada-kota { font-size: 10pt; padding-left: 20px; }

        /* DATA TABLE */
        table.data-table {
            width: 95%;
            border-collapse: collapse;
            margin: 6px 0 6px 20px;
        }

        table.data-table td {
            font-size: 10pt;
            vertical-align: top;
            padding: 1px 0;
        }

        table.data-table td.col-label { width: 38%; }
        table.data-table td.col-sep   { width: 3%; text-align: center; }
        table.data-table td.col-value { width: 59%; }

        /* NARASI */
        .narasi {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 8px;
            line-height: 1.5;
            text-indent: 30px;
        }

        /* DAFTAR KELENGKAPAN */
        ol.kelengkapan {
            font-size: 10pt;
            margin: 4px 0 10px 20px;
            padding-left: 20px;
            line-height: 1.6;
        }

        /* CATATAN */
        ol.catatan {
            font-size: 10pt;
            margin: 4px 0 8px 20px;
            padding-left: 20px;
            line-height: 1.6;
        }

        .penutup {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 6px;
            line-height: 1.5;
            text-indent: 30px;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .ttd-right-cell {
            text-align: center;
            width: 45%;
            font-size: 10pt;
        }

        .ttd-spacer { height: 40px; }
        .ttd-nama   { font-size: 10pt; font-weight: bold; }
        .ttd-jabatan { font-size: 10pt; }
        .ttd-nip    { font-size: 10pt; }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 1cm;
            left: 2cm;
            right: 2cm;
            font-size: 5pt;
            color: #333;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-family: Arial, sans-serif;
        }

        .footer-list { list-style-type: none; padding: 0; margin: 0; }
        .footer-list li { margin-bottom: 2px; position: relative; padding-left: 12px; }
        .footer-list li:before { content: "•"; position: absolute; left: 0; }
    </style>
</head>

<body>
    <div class="page">

        {{-- ===== HEADER KECAMATAN ===== --}}
        @if($kelurahan->kecamatan && $kelurahan->kecamatan->kop_surat_path && file_exists(storage_path('app/public/' . $kelurahan->kecamatan->kop_surat_path)))
        <div style="border-bottom: 4px solid #000; padding-bottom: 6px; margin-bottom: 6px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kecamatan->kop_surat_path) }}"
                style="width: 100%; max-height: 110px; object-fit: contain;" alt="Kop Surat">
        </div>
        @else
        <table class="header-table">
            <tr>
                <td style="width:80px; text-align:center; vertical-align:middle;">
                    <div style="width:65px;height:65px;border:2px solid #aaa;border-radius:50%;line-height:65px;font-size:7pt;color:#999;text-align:center;">LOGO</div>
                </td>
                <td style="text-align:center; vertical-align:middle; padding: 0 10px;">
                    <div class="header-text-line1">PEMERINTAH KOTA BANJARBARU</div>
                    <div class="header-text-line2">KECAMATAN {{ strtoupper($kelurahan->kecamatan->nama ?? 'LANDASAN ULIN') }}</div>
                    <div class="header-text-line4">
                        Alamat : Jalan Kenangan RT. 06 RW. IX Kelurahan Landasan Ulin Timur, Telp./Faks : (0511) 4705080
                        &nbsp; Website : kec-landasanulin.banjarbarukota.go.id | Email : admin@kec-landasanulin.banjarbarukota.go.id
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- ===== PHP DATA ===== --}}
        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglSuratPengantar = isset($data['tanggal_surat_pengantar'])
            ? \Carbon\Carbon::parse($data['tanggal_surat_pengantar'])->translatedFormat('d F Y')
            : '-';
        @endphp

        {{-- ===== TANGGAL KANAN ===== --}}
        <p class="tanggal-kanan">Banjarbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>

        {{-- ===== BLOK SURAT ===== --}}
        <table class="blok-surat">
            <tr>
                <td class="lbl">Nomor</td>
                <td class="sep">:</td>
                <td class="val">{{ $permohonan->nomor_surat ?? '400.3.4/....../.../KEL.LU/......' }}</td>
            </tr>
            <tr>
                <td class="lbl">Sifat</td>
                <td class="sep">:</td>
                <td class="val">Penting</td>
            </tr>
            <tr>
                <td class="lbl">Lampiran</td>
                <td class="sep">:</td>
                <td class="val">1 (Satu) Berkas</td>
            </tr>
            <tr>
                <td class="lbl">Hal</td>
                <td class="sep">:</td>
                <td class="val">Rekomendasi Surat Izin Penyelanggaraan Kursus dan Pelatihan, Kegiatan Belajar Mengajar</td>
            </tr>
        </table>

        {{-- ===== KEPADA YTH ===== --}}
        <div class="kepada">
            <p class="kepada-nama">Yth. Kepala Dinas Penanaman Modal Dan Pelayanan Terpadu Satu Pintu</p>
            <p class="kepada-kota">Kota Banjarbaru</p>
            <p>Di</p>
            <p class="kepada-kota">Banjarbaru</p>
        </div>

        {{-- ===== NARASI PEMBUKA ===== --}}
        <p class="narasi">
            Berdasarkan Surat Pengantar Rekomendasi Surat Izin Penyelanggaraan Kursus Dan Pelatihan, Kegiatan Belajar Mengajar
            dari Lurah {{ $kelurahan->nama }} Nomor:
            {{ $data['no_surat_pengantar'] ?? '....' }}
            tanggal {{ $tglSuratPengantar }},
            permohonan untuk mendapatkan Rekomendasi Surat Izin Penyelanggaraan Kursus Dan Pelatihan, Kegiatan Belajar Mengajar
            atas nama :
        </p>

        {{-- DATA PEMOHON & LEMBAGA --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama Pemohon</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nama_lengkap'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">NIK</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nik_bersangkutan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_lengkap'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Nama Lembaga/Kursus</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nama_lembaga'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Materi Lembaga/Kursus</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['materi_lembaga'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Lama Kegiatan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['lama_kegiatan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat Tempat Kegiatan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_lembaga'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- KELENGKAPAN ADMINISTRASI --}}
        <p class="narasi" style="text-indent:0;">Dengan kelengkapan administrasi yang dilampirkan pemohon sebagai berikut :</p>
        <ol class="kelengkapan">
            <li>Surat Pengantar RT/RW Setempat</li>
            <li>Fotokopi KTP dan KK Pemohon</li>
            <li>Surat Permohonan Izin Penyelenggaraan Kursus</li>
            <li>Struktur Organisasi</li>
            <li>Fotokopi Ijazah Kompetensi Penyelenggara dan Tenaga Pengajar</li>
            <li>Izin Tetangga (diketahui Ketua RT dilengkapi Fotokopi KTP)</li>
            <li>Daftar Fasilitas Kelengkapan Belajar dan Warga Belajar</li>
            <li>Daftar Silabus Pembelajaran</li>
            <li>Surat Pengantar Rekomendasi Izin Penyelanggaraan Kursus dan Pelatihan, Kegiatan Belajar Mengajar dari Lurah {{ $kelurahan->nama }}</li>
            <li>Bukti Tanda Lunas PBB-P2 Tahun Berjalan</li>
        </ol>

        {{-- PARAGRAF PENUTUP --}}
        <p class="narasi">
            Dengan ini kami teruskan permohonan Rekomendasi surat pengantar rekomendasi surat izin penyelanggaraan kursus dan pelatihan,
            kegiatan belajar Mengajar untuk diproses lebih lanjut sesuai dengan peraturan yang berlaku,
            dengan beberapa hal yang menjadi catatan sebagai berikut :
        </p>
        <ol class="catatan">
            <li>Pemohon mensosialisasikan kepada warga/masyarakat sekitar tentang tempat kegiatan dan jenis kegiatan</li>
            <li>Melengkapi persyaratan lain apabila dikemudian hari diperlukan</li>
        </ol>

        <p class="narasi">
            Apabila dikemudian hari ternyata terdapat kekeliruan dalam Surat Rekomendasi Surat Izin Penyelanggaraan Kursus Dan Pelatihan,
            Kegiatan Belajar Mengajar ini, maka akan diubah/diperbaiki dan ditinjau kembali sebagaimana mestinya.
        </p>

        <p class="penutup" style="text-indent:0;">Demikian disampaikan sebagai bahan proses selanjutnya</p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:55%;"></td>
                <td class="ttd-right-cell">
                    <p>Camat {{ $kelurahan->kecamatan->nama ?? 'Landasan Ulin' }}</p>
                    <div class="ttd-spacer"></div>
                    @if(isset($qrBase64))
                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:60px;height:60px;" alt="QR Status">
                    @endif
                    <div class="ttd-spacer"></div>
                    <div class="ttd-nama">{{ $kelurahan->kecamatan->camat_nama ? strtoupper($kelurahan->kecamatan->camat_nama) : '____________________' }}</div>
                    @if($kelurahan->kecamatan->camat_pangkat ?? null)
                    <div class="ttd-jabatan">{{ $kelurahan->kecamatan->camat_pangkat }}{{ ($kelurahan->kecamatan->camat_golongan ?? null) ? ' / ' . $kelurahan->kecamatan->camat_golongan : '' }}</div>
                    @endif
                    <div class="ttd-nip">NIP. {{ $kelurahan->kecamatan->camat_nip ?? '-' }}</div>
                </td>
            </tr>
        </table>

    </div>
    <div class="footer">
        <ul class="footer-list">
            <li>UU ITE No 11 Tahun 2008 Pasal 5 Ayat 1 "Informasi Elektronik dan/atau Dokumen Elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah"</li>
            <li>Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSRe</li>
            <li>Dicetak dengan SiMPEL</li>
        </ul>
    </div>
</body>

</html>
