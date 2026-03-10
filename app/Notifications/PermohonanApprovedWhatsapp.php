<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanApprovedWhatsapp extends Notification
{
    use Queueable;

    public $permohonan;
    public $namaPejabat;

    public function __construct(PermohonanSurat $permohonan, $namaPejabat = null)
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
        $isApplicant = $notifiable instanceof \App\Models\PermohonanSurat;

        // Jika sudah completed (final approval)
        if ($p->status === 'completed') {
            if ($isApplicant) {
                return "Halo {$p->nama_pemohon},\n\n" .
                    "✅ Permohonan surat *{$p->jenisSurat->nama}* Anda telah *SELESAI* diproses dan ditandatangai secara elektronik.\n\n" .
                    "Nomor Surat: *{$p->nomor_surat}*\n" .
                    "Tanggal Surat: {$p->tanggal_surat->format('d/m/Y')}\n\n" .
                    "Silahkan download file surat pian melalui link\n" .
                    route('layanan.surat.tracking') . " dan masukkan Kode Tracking: *{$p->track_token}*\n\n" .
                    "Atau jika kesulitan, Silakan datang ke kantor kelurahan untuk mengambil surat Anda.\n\n" .
                    "Terima kasih.";
            }
        }

        // Jika masih in_review / approved
        if ($isApplicant) {
            return "Halo {$p->nama_pemohon},\n\n" .
                "📋 Permohonan surat *{$p->jenisSurat->nama}* Anda telah *DISETUJUI* pada tahap verifikasi saat ini.\n\n" .
                "Permohonan Anda sedang dilanjutkan ke tahap berikutnya.\n\n" .
                "Kode Tracking: *{$p->track_token}*\n" .
                "Terima kasih atas kesabarannya.";
        }

        // Untuk notifikasi ke Penandatangan Surat (Lurah / Camat)
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
