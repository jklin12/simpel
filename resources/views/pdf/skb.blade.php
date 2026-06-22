<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Bepergian</title>
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
        .ttd-nama   { font-size: 10pt;}
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
        <div class="surat-title">Surat Keterangan Bepergian</div>
        <div class="surat-nomor">
            Nomor : {{ $permohonan->nomor_surat ?? '400.12.5.3/......-SMPL/.../.........../......' }}
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
                <td class="col-value">{{ $kelurahan->signer_jabatan }}</td>
            </tr>
        </table>

        {{-- ===== PHP DATA ===== --}}
        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglLahir = isset($data['tanggal_lahir'])
            ? \Carbon\Carbon::parse($data['tanggal_lahir'])->translatedFormat('d F Y')
            : '-';
        $tglSuratPengantar = isset($data['tanggal_surat_pengantar'])
            ? \Carbon\Carbon::parse($data['tanggal_surat_pengantar'])->translatedFormat('d F Y')
            : '-';
        $tglBerangkat = isset($data['tanggal_berangkat'])
            ? \Carbon\Carbon::parse($data['tanggal_berangkat'])->translatedFormat('d F Y')
            : '-';
        $rtPad = str_pad($data['rt'] ?? '...', 3, '0', STR_PAD_LEFT);
        $rwPad = str_pad($data['rw'] ?? '...', 3, '0', STR_PAD_LEFT);
        $pengikutLines = array_values(array_filter(array_map('trim', explode("\n", $data['pengikut'] ?? ''))));
        @endphp

        {{-- MENERANGKAN --}}
        <p class="section-margin" style="font-size:10pt;">Menerangkan bahwa :</p>

        {{-- DATA PEMOHON --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($data['nama_lengkap'] ?? $permohonan->nama_pemohon) }}</td>
            </tr>
            <tr>
                <td class="col-label">NIK</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nik_bersangkutan'] ?? $permohonan->nik_pemohon }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat/Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ ($data['tempat_lahir'] ?? '-') . ', ' . strtoupper($tglLahir) }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ ($data['alamat_lengkap'] ?? $permohonan->alamat_pemohon) . ' RT. ' . $rtPad . ' RW. ' . $rwPad }}</td>
            </tr>
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['jenis_kelamin'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Warga Negara</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['warga_negara'] ?? 'WNI' }}</td>
            </tr>
            <tr>
                <td class="col-label">Agama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['agama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Status Perkawinan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['status_perkawinan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Pekerjaan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['pekerjaan'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- ===== NARASI ===== --}}
        <p class="narasi" style="margin-top:6px;">
            Berdasarkan surat pengantar Ketua RT. {{ $rtPad }}
            RW. {{ $rwPad }}
            Nomor: {{ $data['no_surat_pengantar'] ?? '....' }}
            tanggal {{ $tglSuratPengantar }}, bahwa nama tersebut diatas benar penduduk di Kelurahan
            {{ $kelurahan->nama }} yang diketahui keberangkatannya dari Kelurahan {{ $kelurahan->nama }}
            Kecamatan Landasan Ulin Kota Banjarbaru:
        </p>

        {{-- DATA PERJALANAN --}}
        <table class="data-table" style="margin-bottom:4px;">
            <tr>
                <td class="col-label">Tujuan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['tujuan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Untuk Keperluan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['keperluan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Pada Hari/Tanggal</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ ($data['hari_berangkat'] ?? '-') . ', ' . strtoupper($tglBerangkat) }}</td>
            </tr>
            <tr>
                <td class="col-label" style="vertical-align:top;">Pengikut</td>
                <td class="col-sep" style="vertical-align:top;">:</td>
                <td class="col-value">
                    @if(!empty($pengikutLines))
                        @foreach($pengikutLines as $i => $item)
                            {{ ($i + 1) . '. ' . $item }}<br>
                        @endforeach
                    @else
                        1â€¦â€¦<br>2â€¦â€¦<br>dst
                    @endif
                </td>
            </tr>
        </table>

        <p class="penutup" style="margin-top:8px;">
            Demikian surat keterangan bepergian ini diberikan untuk dapat dpergunakan
            sebagaimana mestinya.
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

