<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanRejectedWhatsapp extends Notification
{
    use Queueable;

    public $permohonan;
    public $reason;

    public function __construct(PermohonanSurat $permohonan, string $reason)
    {
        $this->permohonan = $permohonan;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable)
    {
        $p = $this->permohonan;

        return "Halo {$p->nama_pemohon},\n\n" .
            "❌ Mohon maaf, permohonan surat *{$p->jenisSurat->nama}* Anda *DITOLAK*.\n\n" .
            "Alasan: {$this->reason}\n\n" .
            "Silakan perbaiki data dan ajukan kembali, atau hubungi kantor kelurahan untuk informasi lebih lanjut.\n\n" .
            "Kode Tracking: *{$p->track_token}*\n" .
            "Terima kasih.";
    }
}
