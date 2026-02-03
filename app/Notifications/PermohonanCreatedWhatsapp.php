<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanCreatedWhatsapp extends Notification
{
    use Queueable;

    public $permohonan;

    /**
     * Create a new notification instance.
     */
    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable)
    {
        return "Halo {$this->permohonan->nama_pemohon},\n\n" .
            "Permohonan surat *{$this->permohonan->jenisSurat->nama}* Anda telah kami terima.\n\n" .
            "Kode Tracking: *{$this->permohonan->track_token}*\n\n" .
            "Gunakan kode ini untuk mengecek status permohonan Anda di website kami.\n" .
            "Terima kasih.";
    }
}
