<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Pengantar Keterangan Domisili Kepartaian</title>
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
            border-bottom: 4px solid #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header-text-line1 { font-size: 11pt; font-weight: normal; }
        .header-text-line2 { font-size: 11pt; font-weight: normal; }
        .header-text-line3 { font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .header-text-line4 { font-size: 8pt; margin-top: 2px; }

        /* JUDUL SURAT */
        .surat-title {
            text-align: center;
            margin: 14px 0 0 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .surat-nomor {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 16px;
        }

        /* YANG BERTANDA TANGAN */
        table.ttd-intro {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.ttd-intro td {
            font-size: 11pt;
            vertical-align: top;
            padding: 2px 0;
        }

        table.ttd-intro td.lbl { width: 25%; }
        table.ttd-intro td.sep { width: 5%; }
        table.ttd-intro td.val { width: 70%; }

        /* NARASI */
        .narasi {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.7;
            text-indent: 30px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 90%;
            border-collapse: collapse;
            margin: 4px 0 12px 20px;
        }

        table.data-table td {
            font-size: 11pt;
            vertical-align: top;
            padding: 2px 0;
        }

        table.data-table td.col-label { width: 35%; }
        table.data-table td.col-sep   { width: 5%; }
        table.data-table td.col-value { width: 60%; }

        /* PENUTUP */
        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 14px;
            line-height: 1.7;
            text-indent: 30px;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .ttd-right-cell {
            text-align: center;
            width: 45%;
            font-size: 11pt;
        }

        .ttd-spacer { height: 40px; }
        .ttd-nama   { font-size: 11pt; font-weight: bold; }
        .ttd-jabatan { font-size: 11pt; }
        .ttd-nip    { font-size: 11pt; }

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

        {{-- ===== HEADER KELURAHAN ===== --}}
        @if($kelurahan->kop_surat_path && file_exists(storage_path('app/public/' . $kelurahan->kop_surat_path)))
        <div style="border-bottom: 4px solid #000; padding-bottom: 6px; margin-bottom: 6px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kop_surat_path) }}"
                style="width: 100%; max-height: 100px; object-fit: contain;" alt="Kop Surat">
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
                    <div class="header-text-line3">KELURAHAN {{ strtoupper($kelurahan->nama) }}</div>
                    <div class="header-text-line4">
                        Alamat : {{ $kelurahan->alamat ?? 'Jalan Kenangan RT. 06 RW. IX Kelurahan Landasan Ulin Timur' }}
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
        $rt = $data['rt'] ?? '...';
        $rw = $data['rw'] ?? '...';
        @endphp

        {{-- ===== JUDUL SURAT ===== --}}
        <div class="surat-title">SURAT PENGANTAR<br>KETERANGAN DOMISILI KEPARTAIAN</div>
        <div class="surat-nomor">Nomor : {{ $permohonan->nomor_surat ?? '200.1.5/....../.../KEL......./......' }}</div>

        {{-- ===== YANG BERTANDA TANGAN ===== --}}
        <p style="font-size:11pt; margin-bottom:6px;">Yang bertanda tangan di bawah ini :</p>
        <table class="ttd-intro">
            <tr>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td class="val">{{ $lurah->lurah_nama ?? ($kelurahan->lurah_nama ?? '-') }}</td>
            </tr>
            <tr>
                <td class="lbl">NIP</td>
                <td class="sep">:</td>
                <td class="val">{{ $lurah->lurah_nip ?? ($kelurahan->lurah_nip ?? '-') }}</td>
            </tr>
            <tr>
                <td class="lbl">Jabatan</td>
                <td class="sep">:</td>
                <td class="val">{{ $kelurahan->signer_jabatan }}</td>
            </tr>
        </table>

        {{-- ===== NARASI PEMBUKA ===== --}}
        <p class="narasi">
            Berdasarkan Surat Pengantar Ketua RT. {{ $rt }} RW. {{ $rw }} Nomor:
            {{ $data['no_surat_pengantar'] ?? '....' }}
            tanggal {{ $tglSuratPengantar }}, dengan ini menerangkan bahwa :
        </p>

        {{-- ===== DATA KANTOR/PARTAI ===== --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama Kantor</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nama_kantor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_kantor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Keperluan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['keperluan'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- ===== PARAGRAF KETERANGAN ===== --}}
        <p class="narasi">
            Bahwa Domisili Kepartaian tersebut sampai saat ini tetap bertempat dilingkungan
            RT. {{ $rt }} RW. {{ $rw }}
            Kelurahan {{ $kelurahan->nama }}
            Kecamatan {{ $kelurahan->kecamatan->nama ?? 'Landasan Ulin' }}
            Pemerintah Kota Banjarbaru.
        </p>

        {{-- ===== PENUTUP ===== --}}
        <p class="penutup">
            Demikian Surat Pengantar Keterangan Domisili Kepartaian ini diberikan untuk diketahui dan
            dipergunakan sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:55%;"></td>
                <td class="ttd-right-cell">
                    <p>Banjarbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>Lurah {{ $kelurahan->nama }}</p>
                    <div class="ttd-spacer"></div>
                    @if(isset($qrBase64))
                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:60px;height:60px;" alt="QR Status">
                    @endif
                    <div class="ttd-spacer"></div>
                    <div class="ttd-nama">{{ strtoupper($lurah->lurah_nama ?? $kelurahan->lurah_nama ?? '____________________') }}</div>
                    @php
                        $pangkat  = $lurah->lurah_pangkat  ?? $kelurahan->lurah_pangkat  ?? null;
                        $golongan = $lurah->lurah_golongan ?? $kelurahan->lurah_golongan ?? null;
                    @endphp
                    @if($pangkat)
                    <div class="ttd-jabatan">{{ $pangkat }}{{ $golongan ? ' / ' . $golongan : '' }}</div>
                    @endif
                    <div class="ttd-nip">NIP. {{ $lurah->lurah_nip ?? $kelurahan->lurah_nip ?? '-' }}</div>
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

