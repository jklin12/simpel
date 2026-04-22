<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanApprovedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'approved';

    public $permohonan;

    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable)
    {
        $p = $this->permohonan;

        // Jika sudah completed (final approval)
        if ($p->status === 'completed') {
            return "Halo {$p->nama_pemohon},\n\n" .
                "✅ Permohonan surat *{$p->jenisSurat->nama}* Anda telah *SELESAI* diproses dan ditandatangai secara elektronik.\n\n" .
                "Nomor Surat: *{$p->nomor_surat}*\n" .
                "Tanggal Surat: {$p->tanggal_surat->format('d/m/Y')}\n\n" .
                "Silahkan download file surat pian melalui link berikut:\n" .
                route('layanan.surat.tracking.search', ['track_token' => $p->track_token]) . "\n\n" .
                "Atau jika kesulitan, Silakan datang ke kantor kelurahan untuk mengambil surat Anda.\n\n" .
                "Terima kasih.";
        }

        // Jika masih in_review / approved
        return "Halo {$p->nama_pemohon},\n\n" .
            "📋 Permohonan surat *{$p->jenisSurat->nama}* Anda telah *DISETUJUI* pada tahap verifikasi saat ini.\n\n" .
            "Permohonan Anda sedang dilanjutkan ke tahap berikutnya.\n\n" .
            "Pantau status permohonan Anda di sini:\n" .
            route('layanan.surat.tracking.search', ['track_token' => $p->track_token]) . "\n\n" .
            "Terima kasih atas kesabarannya.";
    }
}
