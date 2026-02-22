<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Belum Memiliki Rumah</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
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

        .hl {
            color: #000;
            font-weight: bold;
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
            padding: 1.5px 0;
        }

        table.data-table td.col-label {
            width: 42%;
        }

        table.data-table td.col-sep {
            width: 3%;
            text-align: center;
        }

        table.data-table td.col-value {
            width: 55%;
        }

        table.data-table td.col-value-bold {
            width: 55%;
            font-weight: bold;
            color: #000;
        }

        /* NARASI */
        .narasi {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 10px;
            line-height: 1.7;
        }

        .penutup {
            font-size: 11pt;
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.7;
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

        .ttd-jabatan {
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
                        @if($kelurahan->website ?? null)
                        &nbsp; Website : {{ $kelurahan->website }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        @endif


        {{-- ===== JUDUL ===== --}}
        <div class="surat-title">Surat Keterangan Belum Memiliki Rumah</div>
        <div class="surat-nomor">
            Nomor :  {{ $permohonan->nomor_surat ?? '600.2/....../...../.........../......' }} 
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

        {{-- MENERANGKAN --}}
        <p class="section-margin" style="font-size:11pt;">Menerangkan bahwa &nbsp;:</p>

        {{-- DATA PEMOHON --}}
        @php
        $data = $permohonan->data_permohonan ?? [];
        $tglLahir = isset($data['tanggal_lahir'])
        ? \Carbon\Carbon::parse($data['tanggal_lahir'])->translatedFormat('d F Y')
        : '-';
        $tglPengantar = isset($data['tanggal_surat_pengantar'])
        ? \Carbon\Carbon::parse($data['tanggal_surat_pengantar'])->translatedFormat('d F Y')
        : '-';
        $tglSurat = $permohonan->tanggal_surat
        ? \Carbon\Carbon::parse($permohonan->tanggal_surat)->translatedFormat('d F Y')
        : \Carbon\Carbon::now()->translatedFormat('d F Y');
        $rtPad = str_pad($data['rt'] ?? '...', 3, '0', STR_PAD_LEFT);
        $rwPad = str_pad($data['rw'] ?? '...', 3, '0', STR_PAD_LEFT);
        @endphp

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
                <td class="col-value">{{ ($data['tempat_lahir'] ?? '-') . ', ' . $tglLahir }}</td>
            </tr>
            <tr>
                <td class="col-label">Alamat</td>
                <td class="col-sep">:</td>
                <td class="col-value">
                    {{ $data['alamat_lengkap'] ?? $permohonan->alamat_pemohon }}
                    RT. {{ $rtPad }} RW. {{ $rwPad }}
                    Kel. {{ ucfirst($kelurahan->nama) }}
                    Kec. {{ ucfirst($kelurahan->kecamatan->nama ?? '-') }}
                    Kota Banjarbaru
                </td>
            </tr>
            <tr>
                <td class="col-label">Jenis Kelamin</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['jenis_kelamin'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Agama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['agama'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Pekerjaan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['pekerjaan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Pendidikan Terakhir</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['pendidikan_terakhir'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="col-label">Keperluan Untuk</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['keperluan'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- ===== NARASI ===== --}}
        <p class="narasi" style="margin-top:10px;">
            Berdasarkan surat pengantar Ketua RT.  {{ $rtPad }} 
            RW. {{ $rwPad }} 
            Nomor: {{ $data['no_surat_pengantar'] ?? '....' }} 
            tanggal {{ $tglPengantar }},
            dan surat pernyataan pemohon bahwa yang namanya tersebut diatas belum memiliki rumah/tempat tinggal sendiri.
        </p>

        <p class="penutup">
            Demikian surat keterangan belum memiliki rumah ini diberikan untuk dapat dpergunakan
            sebagaimana mestinya.
        </p>

        {{-- ===== TANDA TANGAN ===== --}}
        <table class="ttd-table">
            <tr>
                <td style="width:50%;"></td>
                <td class="ttd-right-cell">
                    <p>Banjarbaru, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>a.n Camat Landasan Ulin</p>
                    <p>Lurah  {{ ucwords(strtolower($kelurahan->nama)) }}</p>
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
</body>

</html>