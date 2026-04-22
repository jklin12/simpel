<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Dispensasi Nikah</title>
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
            padding: 1cm 2cm 3.5cm 2cm;
        }

        /* HEADER */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 2px;
        }

        table.header-table td {
            vertical-align: middle;
        }

        .header-text-line1 {
            font-size: 13pt;
            font-weight: normal;
        }

        .header-text-line2 {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .header-text-line4 {
            font-size: 8pt;
            margin-top: 2px;
        }

        .border-bottom-double {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 10px;
        }

        /* TITLE */
        .surat-title {
            text-align: center;
            margin: 10px 0 0px 0;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .surat-nomor {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 14px;
        }

        /* NARASI */
        .narasi {
            font-size: 9pt;
            text-align: justify;
            margin-bottom: 8px;
            text-indent: 40px;
        }

        .narasi-no-indent {
            font-size: 9pt;
            text-align: justify;
            margin-bottom: 1px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table td {
            font-size: 9pt;
            vertical-align: top;
            padding: 2px 0;
        }

        table.data-table td.col-label {
            width: 35%;
        }

        table.data-table td.col-sep {
            width: 3%;
            text-align: center;
        }

        table.data-table td.col-value {
            width: 62%;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .ttd-right-cell {
            text-align: center;
            width: 50%;
            font-size: 10pt;
        }

        .ttd-spacer {
            height: 10px;
        }

        .ttd-nama {
            font-size: 10pt; 
            /* As in example image */
        }

        .ttd-nip {
            font-size: 10pt; 
        }

        .ttd-jabatan {
            font-size: 10pt; 
        }

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

        .footer-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .footer-list li {
            margin-bottom: 2px;
            position: relative;
            padding-left: 12px;
        }

        .footer-list li:before {
            content: "•";
            position: absolute;
            left: 0;
        }
    </style>
</head>

<body>
    <div class="page">
        {{-- ===== HEADER KECAMATAN ===== --}}
        @if($kelurahan->kecamatan && $kelurahan->kecamatan->kop_surat_path && file_exists(storage_path('app/public/' . $kelurahan->kecamatan->kop_surat_path)))
        <div style="border-bottom: 2px solid #000; padding-bottom: 6px; margin-bottom: 12px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kecamatan->kop_surat_path) }}" style="width: 100%; max-height: 120px; object-fit: contain;" alt="Kop Surat">
        </div>
        @else
        <table class="header-table">
            <tr>
                <td style="width:100px; text-align:center;">
                    <!-- Adjust logo path as needed, or use a default one -->
                    @if($kelurahan->kecamatan && $kelurahan->kecamatan->logo_path && file_exists(storage_path('app/public/' . $kelurahan->kecamatan->logo_path)))
                    <img src="{{ storage_path('app/public/' . $kelurahan->kecamatan->logo_path) }}" style="width: 80px; height: auto;" alt="Logo">
                    @else
                    <!-- Fallback Logo Banjarbaru (if local public folder has logo.png or similar, otherwise empty block) -->
                    @if(file_exists(public_path('images/logo-banjarbaru.png')))
                    <img src="{{ public_path('images/logo-banjarbaru.png') }}" style="width: 80px; height: auto;" alt="Logo Banjarbaru">
                    @else
                    <div style="width:80px;height:90px;border:2px solid #aaa;text-align:center;line-height:90px;">LOGO</div>
                    @endif
                    @endif
                </td>
                <td style="text-align:center; padding-right: 100px;">
                    <div class="header-text-line1">PEMERINTAH KOTA BANJARBARU</div>
                    <div class="header-text-line2">KECAMATAN {{ strtoupper($kelurahan->kecamatan->nama ?? 'LANDASAN ULIN') }}</div>
                    <div class="header-text-line4">
                        Alamat : Jalan Kenangan RT. 06 RW. IX Kelurahan Landasan Ulin Timur, Telp./Faks : (0511) 4705080<br>
                        Website : kec-landasanulin.banjarbarukota.go.id | Email : admin@kec-landasanulin.banjarbarukota.go.id | IG @landasan.ulin
                    </div>
                </td>
            </tr>
        </table>
        <div class="border-bottom-double"></div>
        @endif

        {{-- ===== JUDUL ===== --}}
        <div class="surat-title">SURAT DISPENSASI NIKAH</div>
        <div class="surat-nomor">
            Nomor : {!! str_replace(['/', ' '], ['<span style="color:red">/</span>', ' '], $permohonan->nomor_surat ?? '400.12.3.2 / 000 / I / KEC.LU / ' . date('Y')) !!}</div>

        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglPermohonan = $permohonan->created_at ? $permohonan->created_at->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y');

        // Pemohon Data
        $namaPemohon = strtoupper($data['nama_lengkap'] ?? $permohonan->nama_pemohon ?? '-');
        $jkPemohon = ($data['jenis_kelamin'] ?? '') == 'PEREMPUAN' ? 'Perempuan' : 'Laki - Laki';
        $tglLahirPemohon = isset($data['tanggal_lahir']) ? \Carbon\Carbon::parse($data['tanggal_lahir']) : null;
        $strTglLahirPemohon = $tglLahirPemohon ? $tglLahirPemohon->translatedFormat('d F Y') : '-';
        $umurPemohon = $tglLahirPemohon ? $tglLahirPemohon->age : '-';

        // Pasangan Data
        $namaPasangan = strtoupper($data['nama_pasangan'] ?? '-');
        $jkPasangan = ($data['jenis_kelamin_pasangan'] ?? '') == 'PEREMPUAN' ? 'Perempuan' : 'Laki - Laki';
        $tglLahirPasangan = isset($data['tanggal_lahir_pasangan']) ? \Carbon\Carbon::parse($data['tanggal_lahir_pasangan']) : null;
        $strTglLahirPasangan = $tglLahirPasangan ? $tglLahirPasangan->translatedFormat('d F Y') : '-';
        $umurPasangan = $tglLahirPasangan ? $tglLahirPasangan->age : '-';

        // Pelaksanaan
        $tglPernikahan = isset($data['tanggal_pernikahan']) ? \Carbon\Carbon::parse($data['tanggal_pernikahan']) : null;
        $hariPernikahan = $data['hari_pernikahan'] ?? ($tglPernikahan ? $tglPernikahan->translatedFormat('l') : '-');
        $strTglPernikahan = $tglPernikahan ? $tglPernikahan->translatedFormat('d F Y') : '-';
        @endphp

        {{-- ===== PEMBUKA ===== --}}
        <p class="narasi">
            Berdasarkan Surat Permohonan sdr/i {{ $namaPemohon }} Tanggal {{ $tglPermohonan }} dengan Alamat {{ ucfirst(strtolower($data['alamat_lengkap'])) ?? '-' }} sebagai persyaratan untuk melaksanakan Pernikahan, dengan ini menerangkan bahwa :
        </p>

        {{-- DATA PEMOHON --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $namaPemohon }}</td>
            </tr>
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($jkPemohon) }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat / Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['tempat_lahir'] ?? '-')) }}, {{ strtoupper($strTglLahirPemohon) }}</td>
            </tr>
            <tr>
                <td class="col-label">Umur</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $umurPemohon }} Tahun</td>
            </tr>
            <tr>
                <td class="col-label">Pekerjaan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['pekerjaan'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Agama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['agama'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Status</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['status_perkawinan'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_lengkap'] ?? '-' }}</td>
            </tr>
        </table>

        <p class="narasi-no-indent">
            Akan melangsungkan pernikahan dengan :
        </p>

        {{-- DATA PASANGAN --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $namaPasangan }}</td>
            </tr>
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($jkPasangan) }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat / Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['tempat_lahir_pasangan'] ?? '-')) }}, {{ strtoupper($strTglLahirPasangan) }}</td>
            </tr>
            <tr>
                <td class="col-label">Umur</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $umurPasangan }} Tahun</td>
            </tr>
            <tr>
                <td class="col-label">Pekerjaan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['pekerjaan_pasangan'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Agama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['agama_pasangan'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Status</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ (strtoupper($data['status_perkawinan_pasangan'] ?? '-')) }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_pasangan'] ?? '-' }}</td>
            </tr>
        </table>

        <p class="narasi-no-indent">
            Pernikahan tersebut akan dilaksanakan pada :
        </p>

        {{-- PELAKSANAAN --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Hari / Tanggal</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $hariPernikahan }} / {{ strtoupper($strTglPernikahan) }}</td>
            </tr>
            <tr>
                <td class="col-label">Waktu</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['waktu_pernikahan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Bertempat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['tempat_pernikahan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_pernikahan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Alasan dari mempelai</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alasan_dispensasi'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- PENUTUP --}}
        <p class="narasi">
            Pada prinsipnya kami tidak keberatan akan maksud tersebut, sepanjang yang bersangkutan memenuhi ketentuan dan persyaratan yang berlaku, dan selanjutnya akan diserahkan kepada yang berwenang untuk menindak lanjutinya.
        </p>
        <p class="narasi" style="text-indent: 40px; margin-top: -5px;">
            Demikian Surat Dispensasi Nikah ini diberikan kepada yang bersangkutan untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:50%;"></td>
                <td class="ttd-right-cell">
                    <p style="color: black;">Banjarbaru, <span style="">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span></p>
                    <p style="color: black;">Camat {{ (strtoupper($kelurahan->kecamatan->nama ?? 'Landasan Ulin')) }}</p>
                    <div class="ttd-spacer"></div>
                    @if(isset($qrBase64))
                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:70px;height:70px;" alt="QR Status">
                    @else
                    <!-- Dummy QR for preview -->
                    <div style="width:70px; height:70px; border:1px solid #000; display:inline-block; line-height:70px;">[QR]</div>
                    @endif
                    <div class="ttd-spacer"></div>
                    <div class="ttd-nama">{{ $kelurahan->kecamatan->camat_nama ? strtoupper($kelurahan->kecamatan->camat_nama) : 'DINNY WAHYUNY, S.STP' }}</div>
                    @if($kelurahan->kecamatan->camat_pangkat)
                    <div class="ttd-jabatan">{{ $kelurahan->kecamatan->camat_pangkat }}{{ $kelurahan->kecamatan->camat_golongan ? ' / ' . $kelurahan->kecamatan->camat_golongan : '' }}</div>
                    @else
                    <div class="ttd-jabatan">Pembina Tingkat I</div>
                    @endif
                    <div class="ttd-nip">NIP. {{ $kelurahan->kecamatan->camat_nip ?? '19800723 199810 2 001' }}</div>
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