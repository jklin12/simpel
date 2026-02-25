<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Kematian</title>
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
            padding: 1.5cm 2cm 2cm 2.5cm;
        }

        /* HEADER */
        table.header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px solid #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header-text-line1 {
            font-size: 11pt;
            font-weight: normal;
        }

        .header-text-line2 {
            font-size: 11pt;
            font-weight: normal;
        }

        .header-text-line3 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-text-line4 {
            font-size: 8pt;
            margin-top: 2px;
        }

        /* TITLE */
        .surat-title {
            text-align: center;
            margin: 14px 0 0 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .surat-nomor {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 14px;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        table.data-table td {
            font-size: 11pt;
            vertical-align: top;
            padding: 2px 0;
        }

        table.data-table td.col-label {
            width: 25%;
        }

        table.data-table td.col-sep {
            width: 3%;
            text-align: center;
        }

        table.data-table td.col-value {
            width: 72%;
        }

        .section-label {
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 5px;
            font-size: 11pt;
        }

        .col-value-indent {
            display: inline-block;
        }

        /* NARASI */
        .narasi {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        /* PENUTUP */
        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.5;
            margin-top: 10px;
        }

        /* TTD */
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        .ttd-right-cell {
            text-align: left;
            padding-left: 60%;
            width: 100%;
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
    </style>
</head>

<body>
    <div class="page">

        {{-- ===== HEADER ===== --}}
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
                        Alamat : {{ $kelurahan->alamat ?? 'Kota Banjarbaru' }}
                        &nbsp; Telp. {{ $kelurahan->telp ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- ===== JUDUL ===== --}}
        <div class="surat-title">Surat Keterangan Kematian</div>
        <div class="surat-nomor">
            Nomor : {{ $permohonan->nomor_surat ?? '......../..........' }}
        </div>

        {{-- ===== PEMBUKA ===== --}}
        <p style="font-size:11pt; margin-bottom:8px;">Yang bertanda tangan di bawah ini :</p>

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

        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglLahirJenazah = isset($data['tanggal_lahir_jenazah'])
        ? \Carbon\Carbon::parse($data['tanggal_lahir_jenazah'])->translatedFormat('d F Y')
        : '-';
        $tglPengantar = isset($data['tanggal_pengantar'])
        ? \Carbon\Carbon::parse($data['tanggal_pengantar'])->translatedFormat('d F Y')
        : '-';
        $tglMeninggal = isset($data['tanggal_meninggal'])
        ? \Carbon\Carbon::parse($data['tanggal_meninggal'])->translatedFormat('d F Y')
        : '-';
        @endphp

        {{-- NARASI PENGANTAR --}}
        <p class="narasi" style="margin-top:10px;">
            Berdasarkan surat pengantar Ketua RT. <span>{{ str_pad($data['rt'] ?? '...', 3, '0', STR_PAD_LEFT) }}</span>
            RW. <span>{{ str_pad($data['rw'] ?? '...', 3, '0', STR_PAD_LEFT) }}</span>
            Nomor: <span>{{ $data['nomor_pengantar'] ?? '....' }}</span>
            tanggal <span>{{ $tglPengantar }}</span>,
            Kelurahan <span>{{ ucwords(strtolower($kelurahan->nama)) }}</span>,
            Kecamatan <span>{{ ucwords(strtolower($kelurahan->kecamatan->nama ?? 'Landasan Ulin')) }}</span>
            Pemerintah Kota Banjarbaru dengan ini menerangkan bahwa:
        </p>

        {{-- DATA JENAZAH --}}
        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($data['nama_jenazah'] ?? $permohonan->nama_pemohon ?? '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">NIK</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nik_jenazah'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat/Tanggal Lahir</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ ($data['tempat_lahir_jenazah'] ?? '-') . ', ' . $tglLahirJenazah }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['alamat_jenazah'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ ($data['jk_jenazah'] ?? '') == 'L' ? 'Laki-laki' : (($data['jk_jenazah'] ?? '') == 'P' ? 'Perempuan' : '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">Agama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['agama_jenazah'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Pekerjaan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['pekerjaan_jenazah'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- DETAIL KEMATIAN --}}
        <table class="data-table" style="margin-top:10px;">
            <tr>
                <td class="col-label" style="font-weight: bold;">Telah Meninggal Pada</td>
                <td class="col-sep" style="font-weight: bold;">:</td>
                <td class="col-value"></td>
            </tr>
            <tr>
                <td class="col-label">Hari/Tanggal Meninggal</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['hari_meninggal'] ?? '-' }}, {{ $tglMeninggal }}</td>
            </tr>
            <tr>
                <td class="col-label">Pukul</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['pukul_meninggal'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['tempat_meninggal'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Disebabkan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['sebab_kematian'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Dimakamkan di-</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['tempat_pemakaman'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- DATA PELAPOR --}}
        <table class="data-table" style="margin-top:10px;">
            <tr>
                <td class="col-label" style="font-weight: bold;">Pelapor</td>
                <td class="col-sep" style="font-weight: bold;">:</td>
                <td class="col-value"></td>
            </tr>
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ strtoupper($data['nama_pelapor'] ?? '-') }}</td>
            </tr>
            <tr>
                <td class="col-label">NIK</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nik_pelapor'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Hub Dengan yang meninggal</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['hubungan_pelapor'] ?? '-' }}</td>
            </tr>
        </table>

        <p class="penutup">
            Demikian surat keterangan kematian ini diberikan untuk dapat dipergunakan sebagaimana mestinya.
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
                    <img src="data:image/png;base64,{{ $qrBase64 }}" style="width:60px;height:60px; margin-top:5px; margin-bottom:5px;" alt="QR Status">
                    @else
                    <br><br><br><br>
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
</body>

</html>