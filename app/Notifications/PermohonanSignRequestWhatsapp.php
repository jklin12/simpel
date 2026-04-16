<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanSignRequestWhatsapp extends Notification
{
    use Queueable;

    public $permohonan;
    public $namaPejabat;

    public function __construct(PermohonanSurat $permohonan, $namaPejabat = 'Bapak/Ibu')
    {
        $this->permohonan = $permohonan;
        $this->namaPejabat = $namaPejabat;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable)
    {
        $p = $this->permohonan;

        $namaTujuan = $this->namaPejabat ?? ($notifiable->name ?? 'Bapak/Ibu');

        $filename = ($p->nomor_surat)
            ? str_replace('/', '-', $p->nomor_surat) . '.pdf'
            : $p->nomor_permohonan . '.pdf';

        return [
            'to' => $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone,
            'message' => "Halo {$namaTujuan},\n\nAda permohonan surat *{$p->jenisSurat->nama}* atas nama *{$p->nama_pemohon}* yang telah diverifikasi dan siap untuk ditandatangani.\n\nBerikut draft surat terlampir.",
            'file_content' => $this->generatePdfContent($p),
            'filename' => $filename,
        ];
    }

    private function generatePdfContent(PermohonanSurat $permohonan)
    {
        $permohonan->load(['jenisSurat', 'kelurahan.kecamatan']);
        $kelurahan = $permohonan->kelurahan;

        $lurah = [
            'nama' => $kelurahan->lurah_nama ? strtoupper($kelurahan->lurah_nama) : 'KEPALA KELURAHAN',
            'nip'  => $kelurahan->lurah_nip ?? '-',
            'title' => 'Lurah ' . $kelurahan->nama,
        ];

        $kode = strtolower($permohonan->jenisSurat->kode ?? '');
        $pdfView = "pdf.{$kode}";

        if (!\Illuminate\Support\Facades\View::exists($pdfView)) {
            return ''; // Empty if no view
        }

        $trackUrl = route('layanan.surat.tracking.search', ['track_token' => $permohonan->track_token]);
        $qrBase64 = base64_encode(
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(120)
                ->margin(1)
                ->generate($trackUrl)
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($pdfView, [
            'permohonan' => $permohonan,
            'kelurahan'  => $kelurahan,
            'lurah'      => $lurah,
            'qrBase64'   => $qrBase64,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->output(); // Return raw PDF byte string
    }
}
