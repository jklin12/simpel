<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Untuk Nikah</title>
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

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-after: always;
        }

        /* ===== HEADER ===== */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .header-kota {
            font-size: 10pt;
            font-weight: normal;
        }

        .header-kec {
            font-size: 10pt;
            font-weight: normal;
        }

        .header-kel {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-alamat {
            font-size: 7.5pt;
            margin-top: 2px;
        }

        /* ===== PAGE 1 ===== */
        .page1 {
            padding: 1.2cm 2cm 1.5cm 2.5cm;
        }

        .surat-title {
            text-align: center;
            margin: 14px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .surat-title2 {
            text-align: center;
            margin: 14px 0 0 0;
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .surat-nomor {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 18px;
        }

        .surat-nomor2 {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 18px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.data-table td {
            font-size: 12pt;
            font-weight: bold;
            vertical-align: top;
            padding: 2px 0;
        }

        table.data-table td.col-label {
            width: 25%;
        }

        table.data-table td.col-sep {
            width: 4%;
            text-align: center;
        }

        table.data-table td.col-value {
            width: 71%;
        }

        .ring-container {
            text-align: center;
            margin: 30px 0 20px 0;
        }

        .footer-brand {
            position: fixed;
            bottom: 1.2cm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #555;
        }

        /* ===== PAGE 2 ===== */
        .page2 {
            padding: 0.8cm 1.5cm 1.5cm 1.5cm;
        }

        .blanko-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .blanko-no {
            text-align: right;
            margin: 14px 0 14px 0;
            font-size: 9pt;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        table.info-table td {
            font-size: 9pt;
            vertical-align: top;
            padding: 1px 0;
        }

        table.info-table td.il {
            width: 40%;
        }

        table.info-table td.is {
            width: 4%;
        }

        table.info-table td.iv {
            width: 56%;
        }

        .section-label {
            font-size: 9pt;
            font-weight: bold;
            margin: 6px 0 2px 0;
        }

        table.form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.form-table td {
            font-size: 8.5pt;
            vertical-align: top;
            padding: 1px 0;
        }

        table.form-table td.fl {
            width: 40%;
        }

        table.form-table td.fs {
            width: 4%;
        }

        table.form-table td.fv {
            width: 56%;
            font-weight: bold;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .ttd-right-cell {
            text-align: center;
            width: 50%;
            font-size: 11pt;
        }

        .ttd-spacer {
            height: 8px;
        }

        .ttd-nama {
            font-size: 11pt;
        }

        .ttd-nip {
            font-size: 11pt;
        }

        .section-margin {
            margin: 8px 0 4px 0;
        }

        /* FOOTER PAGE 2 */
        .footer-p2 {
            position: fixed;
            bottom: 0.8cm;
            left: 1.5cm;
            right: 1.5cm;
            font-size: 6.5pt;
            color: #333;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .footer-p2 ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-p2 ul li {
            padding-left: 10px;
            position: relative;
            margin-bottom: 1px;
        }

        .footer-p2 ul li::before {
            content: "•";
            position: absolute;
            left: 0;
        }
    </style>
</head>

<body>

    @php
    $data = $permohonan->data_permohonan ?? [];
    $tglLahir = isset($data['tanggal_lahir'])
    ? \Carbon\Carbon::parse($data['tanggal_lahir'])->translatedFormat('d F Y')
    : '-';
    $tglSurat = $permohonan->tanggal_surat
    ? \Carbon\Carbon::parse($permohonan->tanggal_surat)->translatedFormat('d F Y')
    : \Carbon\Carbon::now()->translatedFormat('d F Y');
    $tglAyahLahir = isset($data['ayah_tanggal_lahir'])
    ? \Carbon\Carbon::parse($data['ayah_tanggal_lahir'])->translatedFormat('d F Y')
    : '-';
    $tglIbuLahir = isset($data['ibu_tanggal_lahir'])
    ? \Carbon\Carbon::parse($data['ibu_tanggal_lahir'])->translatedFormat('d F Y')
    : '-';

    // Tentukan bin/binti berdasarkan jenis kelamin
    $jk = strtolower($data['jenis_kelamin'] ?? '');
    $binBinti = ($jk === 'perempuan') ? 'BINTI' : 'BIN';
    $namaAyah = strtoupper($data['ayah_nama'] ?? '-');

    $namaLengkap = strtoupper($data['nama_lengkap'] ?? $permohonan->nama_pemohon);
    $alamatLengkap = strtoupper($data['alamat_lengkap'] ?? $permohonan->alamat_pemohon);
    $logoPath = public_path('images/logo_simpel.png');
    $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    {{-- =====================================
     HALAMAN 1 — Cover Surat Keterangan
     ===================================== --}}
    <div class="page1 page-break">

        {{-- HEADER --}}
        @if($kelurahan->kop_surat_path && file_exists(storage_path('app/public/' . $kelurahan->kop_surat_path)))
        <div style="border-bottom: 4px solid #000; padding-bottom: 4px; margin-bottom: 6px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kop_surat_path) }}"
                style="width: 100%; max-height: 100px; object-fit: contain;" alt="Kop Surat">
        </div>
        @else
        <table class="header-table">
            <tr>
                <td style="width:80px; text-align:center; vertical-align:middle;">
                    <div style="width:65px;height:65px;border:2px solid #aaa;border-radius:50%;line-height:65px;font-size:7pt;color:#999;text-align:center;">LOGO</div>
                </td>
                <td style="text-align:center; vertical-align:middle; padding:0 10px;">
                    <div class="header-kota">PEMERINTAH KOTA BANJARBARU</div>
                    <div class="header-kec">KECAMATAN {{ strtoupper($kelurahan->kecamatan->nama ?? 'LANDASAN ULIN') }}</div>
                    <div class="header-kel">KELURAHAN {{ strtoupper($kelurahan->nama) }}</div>
                    <div class="header-alamat">
                        Alamat : {{ $kelurahan->alamat ?? 'Kota Banjarbaru' }}
                        &nbsp;&nbsp; Telp. {{ $kelurahan->telp ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- JUDUL --}}
        <div class="surat-title">Surat Keterangan Untuk Nikah</div>
        <div class="surat-nomor">
            Nomor : {{ $permohonan->nomor_surat ?? '......../..........' }}
        </div>

        {{-- DATA RINGKAS PEMOHON --}}
        <table class="data-table" style="margin-top: 20px;">
            <tr>
                <td class="col-label">NAMA</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $namaLengkap }}</td>
            </tr>
            <tr>
                <td class="col-label">{{ $binBinti }}</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $namaAyah }}</td>
            </tr>
            <tr>
                <td class="col-label">ALAMAT</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $alamatLengkap }}
                    KEL. {{ strtoupper($kelurahan->nama) }}
                    KEC. {{ strtoupper($kelurahan->kecamatan->nama ?? 'LANDASAN ULIN') }}
                    KOTA BANJARBARU
                </td>
            </tr>
        </table>

        {{-- DEKORASI CINCIN NIKAH --}}
        <div class="ring-container">
            {{-- SVG cincin nikah stilize --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="260" height="150" viewBox="0 0 260 150">
                <!-- cincin kiri -->
                <ellipse cx="90" cy="85" rx="60" ry="28" fill="none" stroke="#bbb" stroke-width="18" />
                <ellipse cx="90" cy="85" rx="60" ry="28" fill="none" stroke="#e8e0c8" stroke-width="14" />
                <!-- cincin kanan -->
                <ellipse cx="170" cy="85" rx="60" ry="28" fill="none" stroke="#ccc" stroke-width="18" />
                <ellipse cx="170" cy="85" rx="60" ry="28" fill="none" stroke="#f5f0e0" stroke-width="14" />
                <!-- batu permata di cincin kiri -->
                <polygon points="85,55 95,45 105,55 95,70" fill="#d0a0a0" stroke="#999" stroke-width="1" />
                <polygon points="85,55 95,45 95,45 85,55" fill="#e8b8b8" stroke="none" />
            </svg>
        </div>

        {{-- LOGO SIMPEL --}}
        @if($logoBase64)
        <div style="text-align:center; margin-top:10px;">
            <img src="{{ $logoBase64 }}" style="height:50px; opacity:0.7;" alt="SiMPEL Logo">
            <div style="font-size:6.5pt; color:#888; margin-top:4px; font-weight:bold; letter-spacing:1px;">
                SISTEM INFORMASI MANAJEMEN PELAYANAN<br>KECAMATAN LANDASAN ULIN
            </div>
        </div>
        @endif

    </div>

    {{-- =====================================
     HALAMAN 2 — Formulir Pengantar Nikah
     ===================================== --}}
    <div class="page2">

        {{-- JUDUL BLANKO --}}
        <div class="blanko-title">Formulir Pengantar Nikah</div>
        <div class="blanko-no"><strong>Blanko N1</strong></div>

        {{-- INFO KANTOR --}}
        <table class="info-table" style="margin-top:6px;">
            <tr>
                <td class="il"><strong>KANTOR DESA/KELURAHAN</strong></td>
                <td class="is">:</td>
                <td class="iv"><strong>{{ strtoupper($kelurahan->nama) }}</strong></td>
            </tr>
            <tr>
                <td class="il"><strong>KECAMATAN</strong></td>
                <td class="is">:</td>
                <td class="iv"><strong>{{ strtoupper($kelurahan->kecamatan->nama ?? 'LANDASAN ULIN') }}</strong></td>
            </tr>
            <tr>
                <td class="il"><strong>KABUPATEN/KOTA</strong></td>
                <td class="is">:</td>
                <td class="iv"><strong>BANJARBARU</strong></td>
            </tr>
        </table>

        <div class="surat-title2">Surat Keterangan Untuk Nikah</div>
        <div class="surat-nomor2">
            <strong>Nomor : {{ $permohonan->nomor_surat ?? '......../..........' }}</strong>
        </div>


        {{-- PENGANTAR NIKAH --}}
        <p style="font-size:10pt; margin-bottom:6px;">
            Yang bertanda tangan dibawah ini menjelaskan dengan sesungguhnya bahwa :
        </p>

        @php
        $statusPerkawinan = strtoupper($data['status_perkawinan'] ?? '-');
        $isLakiLaki = strtolower($data['jenis_kelamin'] ?? '') === 'laki-laki';
        // Status perkawinan pria
        $statusLaki = $isLakiLaki ? $statusPerkawinan : '';
        $statusPerempuan = !$isLakiLaki ? $statusPerkawinan : '';
        // Kewarganegaraan display
        $kwn = $data['kewarganegaraan'] ?? 'WNI';
        $kwnDisplay = $kwn === 'WNI' ? 'Indonesia' : $kwn;
        $ayahKwn = $data['ayah_kewarganegaraan'] ?? 'WNI';
        $ayahKwnDisplay = $ayahKwn === 'WNI' ? 'Indonesia' : $ayahKwn;
        $ibuKwn = $data['ibu_kewarganegaraan'] ?? 'WNI';
        $ibuKwnDisplay = $ibuKwn === 'WNI' ? 'Indonesia' : $ibuKwn;
        @endphp

        {{-- Numbered list format --}}
        <table style="width:100%; border-collapse:collapse; font-size:10pt; margin-bottom:4px;">
            <tr>
                <td style="width:6%; vertical-align:top; padding:1.5px 0;">1.</td>
                <td style="width:48%; vertical-align:top; padding:1.5px 0;">Nama lengkap dan alias</td>
                <td style="width:3%; vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $namaLengkap }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">2.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Nomor Induk Kependudukan (NIK)</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['nik_bersangkutan'] ?? $permohonan->nik_pemohon }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">3.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Jenis Kelamin</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['jenis_kelamin'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">4.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Tempat dan tanggal lahir</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ ($data['tempat_lahir'] ?? '-') . ', ' . $tglLahir }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">5.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Kewarganegaraan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $kwnDisplay }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">6.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Agama</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['agama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">7.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Pekerjaan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['pekerjaan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">8.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Alamat</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">
                    {{ $alamatLengkap }},<br>
                    Kel. {{ ucwords(strtolower($kelurahan->nama)) }}, Kec. {{ ucwords(strtolower($kelurahan->kecamatan->nama ?? 'Landasan Ulin')) }}, Kota Banjarbaru
                </td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;">9.</td>
                <td style="vertical-align:top; padding:1.5px 0;">Status Perkawinan</td>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1px 0;"></td>
                <td style="vertical-align:top; padding:1px 0; padding-left:12px;">a. Laki-laki</td>
                <td style="vertical-align:top; padding:1px 0;">:</td>
                <td style="vertical-align:top; padding:1px 0; font-weight:bold;">{{ $statusLaki }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1px 0;"></td>
                <td style="vertical-align:top; padding:1px 0; padding-left:12px;">b. Perempuan</td>
                <td style="vertical-align:top; padding:1px 0;">:</td>
                <td style="vertical-align:top; padding:1px 0; font-weight:bold;">{{ $statusPerempuan }}</td>
            </tr>
        </table>

        {{-- Data Orang Tua —  Ayah --}}
        <p style="font-size:10pt; margin: 0 0 0 17px;">
            <strong>Adalah benar anak dari perkawinan seorang pria :</strong>
        </p>
        <table style="width:100%; border-collapse:collapse; font-size:10pt; margin-bottom:4px;">
            <tr>
                <td style="width:6%; vertical-align:top; padding:1.5px 0;"></td>
                <td style="width:48%; vertical-align:top; padding:1.5px 0;">Nama lengkap dan alias</td>
                <td style="width:3%; vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ayah_nama'] ?? '-') }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Nomor Induk Kependudukan (NIK)</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['ayah_nik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Tempat dan tanggal lahir</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ ($data['ayah_tempat_lahir'] ?? '-') . ', ' . $tglAyahLahir }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Kewarganegaraan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $ayahKwnDisplay }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Agama</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['ayah_agama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Pekerjaan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ayah_pekerjaan'] ?? '-') }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Alamat</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ayah_alamat'] ?? '-') }}</td>
            </tr>
        </table>

        {{-- Data Orang Tua — Ibu --}}
        <p style="font-size:10pt; margin: 0 0 0 17px;">
            <strong> Dengan seorang wanita :</strong>
        </p>
        <table style="width:100%; border-collapse:collapse; font-size:10pt; margin-bottom:4px;">
            <tr>
                <td style="width:6%; vertical-align:top; padding:1.5px 0;"></td>
                <td style="width:48%; vertical-align:top; padding:1.5px 0;">Nama lengkap dan alias</td>
                <td style="width:3%; vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ibu_nama'] ?? '-') }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Nomor Induk Kependudukan (NIK)</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['ibu_nik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Tempat dan tanggal lahir</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ ($data['ibu_tempat_lahir'] ?? '-') . ', ' . $tglIbuLahir }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Kewarganegaraan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $ibuKwnDisplay }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Agama</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ $data['ibu_agama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Pekerjaan</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ibu_pekerjaan'] ?? '-') }}</td>
            </tr>
            <tr>
                <td style="vertical-align:top; padding:1.5px 0;"></td>
                <td style="vertical-align:top; padding:1.5px 0;">Alamat</td>
                <td style="vertical-align:top; padding:1.5px 0;">:</td>
                <td style="vertical-align:top; padding:1.5px 0; font-weight:bold;">{{ strtoupper($data['ibu_alamat'] ?? '-') }}</td>
            </tr>
        </table>

        <p style="font-size:10pt; margin: 8px 0 4px 0;">
            Demikian keterangan yang kami sampaikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana perlunya.
        </p>


        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:50%;"></td>
                <td class="ttd-right-cell">
                    <p>Banjarbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>a.n Camat Landasan Ulin</p>
                    <p>Lurah {{ ucwords(strtolower($kelurahan->nama)) }}</p>
                    <div class="ttd-spacer"></div>
                    @if(isset($qrBase64))
                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:60px;height:60px;" alt="QR Status">
                    @endif
                    <div class="ttd-spacer"></div>
                    <div class="ttd-nama">{{ $kelurahan->lurah_nama ? strtoupper($kelurahan->lurah_nama) : ($lurah['nama'] ?? '____________________') }}</div>
                    @if($kelurahan->lurah_pangkat)
                    <div class="ttd-jabatan">{{ $kelurahan->lurah_pangkat }}{{ $kelurahan->lurah_golongan ? ' / ' . $kelurahan->lurah_golongan : '' }}</div>
                    @endif
                    <div class="ttd-nip">NIP. {{ $kelurahan->lurah_nip ?? ($lurah['nip'] ?? '-') }}</div>
                </td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <div class="footer-p2">
            <ul>
                <li>UU ITE No 11 Tahun 2008 Pasal 5 Ayat 1 — Informasi Elektronik dan/atau Dokumen Elektronik merupakan alat bukti hukum yang sah</li>
                <li>Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSRe</li>
                <li>Dicetak dengan SiMPEL</li>
            </ul>
        </div>

    </div>

</body>

</html>