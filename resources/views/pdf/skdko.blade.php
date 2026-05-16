<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Domisili Kantor/Sekretariat/Organisasi</title>
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
            padding: 1cm 1.8cm 1.5cm 2cm;
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

        /* TITLE */
        .surat-title {
            text-align: center;
            margin: 8px 0 0 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }

        .surat-nomor {
            text-align: center;
            font-size: 10pt;
            margin-bottom: 8px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        table.data-table td {
            font-size: 10pt;
            vertical-align: top;
            padding: 1px 0;
        }

        table.data-table td.col-label { width: 42%; }
        table.data-table td.col-sep   { width: 3%; text-align: center; }
        table.data-table td.col-value { width: 55%; }

        /* NARASI */
        .narasi {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 4px;
            line-height: 1.5;
        }

        .penutup {
            font-size: 10pt;
            text-align: justify;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .ttd-right-cell {
            text-align: center;
            width: 50%;
            font-size: 10pt;
        }

        .ttd-spacer { height: 40px; }
        .ttd-nama   { font-size: 10pt; font-weight: bold; }
        .ttd-jabatan { font-size: 10pt; }
        .ttd-nip    { font-size: 10pt; }

        .section-margin { margin: 5px 0 3px 0; }

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

        {{-- ===== HEADER ===== --}}
        @if($kelurahan->kop_surat_path && file_exists(storage_path('app/public/' . $kelurahan->kop_surat_path)))
        <div style="border-bottom: 4px solid #000; padding-bottom: 6px; margin-bottom: 6px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kop_surat_path) }}"
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
                    <div class="header-text-line3">KELURAHAN {{ strtoupper($kelurahan->nama) }}</div>
                    <div class="header-text-line4">
                        Alamat : {{ $kelurahan->alamat ?? 'Kota Banjarbaru' }}
                        &nbsp; Telp. {{ $kelurahan->telp ?? '-' }}
                        @if($kelurahan->website ?? null) &nbsp; Website : {{ $kelurahan->website }} @endif
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- ===== JUDUL ===== --}}
        <div class="surat-title">
            Surat Keterangan<br>
            Domisili Kantor/Sekretariat/Organisasi Lainnya
        </div>
        <div class="surat-nomor">
            Nomor : {{ $permohonan->nomor_surat ?? '100.2.2/......-SMPL/.../.........../......' }}
        </div>

        {{-- ===== PEMBUKA ===== --}}
        <p style="font-size:10pt; margin-bottom:4px;">Yang bertanda tangan di bawah ini :</p>

        {{-- DATA LURAH --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $kelurahan->lurah_nama ? strtoupper($kelurahan->lurah_nama) : ($lurah['nama'] ?? 'KEPALA KELURAHAN') }}</td>
            </tr>
            <tr>
                <td class="col-label">NIP</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $kelurahan->lurah_nip ?? ($lurah['nip'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">Jabatan</td>
                <td class="col-sep">:</td>
                <td class="col-value">Lurah</td>
            </tr>
        </table>

        {{-- ===== PHP DATA ===== --}}
        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglSuratPengantar = isset($data['tanggal_surat_pengantar'])
            ? \Carbon\Carbon::parse($data['tanggal_surat_pengantar'])->translatedFormat('d F Y')
            : '-';
        $rtPad = str_pad($data['rt'] ?? '...', 3, '0', STR_PAD_LEFT);
        $rwPad = str_pad($data['rw'] ?? '...', 3, '0', STR_PAD_LEFT);
        $alamatLengkap = ($data['alamat_jalan'] ?? '-')
            . ' RT. ' . $rtPad
            . ' RW. ' . $rwPad
            . ' Kel. ' . $kelurahan->nama
            . ' Kec. Landasan Ulin Kota Banjarbaru';
        @endphp

        {{-- PEMBUKA SURAT PENGANTAR --}}
        <p class="narasi" style="margin-top:8px;">
            Berdasarkan Surat Pengantar RT. {{ $rtPad }}
            RW. {{ $rwPad }}
            Nomor: {{ $data['no_surat_pengantar'] ?? '....' }}
            tanggal {{ $tglSuratPengantar }} dengan ini menerangkan bahwa:
        </p>

        {{-- DATA KANTOR/ORGANISASI --}}
        <table class="data-table" style="margin-bottom:6px;">
            <tr>
                <td class="col-label">Nama Kantor</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($data['nama_kantor'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $alamatLengkap }}</td>
            </tr>
            <tr>
                <td class="col-label">Keperluan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['keperluan'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- ===== NARASI ===== --}}
        <p class="narasi" style="margin-top:6px;">
            Bahwa Domisili Kantor/Sekretariat/Organisasi Lainnya tersebut sampai saat ini tetap
            bertempat di lingkungan RT. {{ $rtPad }} RW. {{ $rwPad }}
            Kelurahan {{ $kelurahan->nama }} Kecamatan Landasan Ulin Kota Banjarbaru.
        </p>

        <p class="penutup" style="margin-top:6px;">
            Demikian surat keterangan Domisili Kantor/Sekretariat/Organisasi Lainnya ini diberikan
            untuk dapat dpergunakan sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:50%;"></td>
                <td class="ttd-right-cell">
                    <p>Banjarbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>a.n Camat Landasan Ulin</p>
                    <p>Lurah {{ $kelurahan->nama }}</p>
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
