<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Keterangan Tidak Mampu</title>
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

        /* CENTER */
        .center {
            text-align: center;
        }

        /* TITLE */
        .surat-title {
            text-align: center;
            margin: 14px 0 0px 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
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

        .narasi .hl {
            color: #000;
            font-weight: bold;
        }

        /* PENUTUP */
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
        {{-- Gunakan gambar kop surat langsung --}}
        <div style="border-bottom: 4px solid #000; padding-bottom: 6px; margin-bottom: 6px; text-align: center;">
            <img src="{{ storage_path('app/public/' . $kelurahan->kop_surat_path) }}"
                style="width: 100%; max-height: 100px; object-fit: contain;" alt="Kop Surat">
        </div>
        @else
        {{-- Fallback: header teks --}}
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
        <div class="surat-title">Surat Keterangan Tidak Mampu</div>
        <div class="surat-nomor">
            Nomor : <strong>{{ $permohonan->nomor_surat ?? '......../..........' }}</strong>
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
        @endphp

        <table class="data-table">
            <tr>
                <td class="col-label">Nama</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['nama_lengkap'] ?? $permohonan->nama_pemohon }}</td>
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
                    RT. {{ $data['rt'] ?? '-' }} RW. {{ $data['rw'] ?? '-' }}
                    Kel. {{ $kelurahan->nama }}
                    Kec. {{ $kelurahan->kecamatan->nama ?? '-' }}
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
                <td class="col-label">Status Perkawinan</td>
                <td class="col-sep">:</td>
                <td class="col-value">{{ $data['status_perkawinan'] ?? '-' }}</td>
            </tr>
        </table>

        {{-- ===== NARASI ===== --}}
        <p class="narasi" style="margin-top:10px;">
            Berdasarkan surat pernyataan pemohon tanggal <span  >{{ $tglSurat }}</span>
            dan surat pengantar Ketua RT. <span  >{{ str_pad($data['rt'] ?? '...', 3, '0', STR_PAD_LEFT) }}</span>
            RW. <span  >{{ str_pad($data['rw'] ?? '...', 3, '0', STR_PAD_LEFT) }}</span>
            Nomor: <span  >{{ $data['no_surat_pengantar'] ?? '....' }}</span>
            tanggal <span  >{{ $tglPengantar }}</span>,
            Kelurahan <span  >{{ ucfirst($kelurahan->nama) }}</span>
            Kecamatan {{ ucfirst($kelurahan->kecamatan->nama) }}
            Pemerintah Kota Banjarbaru dengan ini menerangkan bahwa nama tersebut diatas, tergolong tidak mampu.
        </p>

        <p class="narasi">
            Adapun surat keterangan tidak mampu ini dibuat untuk keperluan
            <span  >{{ $data['keperluan_sktm'] ?? '-' }}</span>@if(!empty($data['keterangan_tambahan'])), {{ $data['keterangan_tambahan'] }}@endif.
        </p>

        <p class="penutup">
            Demikian surat keterangan tidak mampu ini diberikan untuk dapat dipergunakan
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