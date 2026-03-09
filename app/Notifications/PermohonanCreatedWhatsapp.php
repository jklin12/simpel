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
        $p = $this->permohonan;
        $isApplicant = $notifiable instanceof \App\Models\PermohonanSurat;

        if ($isApplicant) {
            return "Halo {$p->nama_pemohon},\n\n" .
                "Permohonan surat *{$p->jenisSurat->nama}* Anda telah kami terima.\n\n" .
                "Kode Tracking: *{$p->track_token}*\n\n" .
                "Gunakan kode ini untuk mengecek status permohonan Anda di website kami.\n" .
                "Terima kasih.";
        }

        // Untuk notifikasi ke Admin
        $adminName = $notifiable->name ?? 'Admin';
        return "Halo {$adminName},\n\n" .
            "Terdapat permohonan surat *baru* untuk layanan *{$p->jenisSurat->nama}* atas nama *{$p->nama_pemohon}*.\n\n" .
            "Kode Tracking: *{$p->track_token}*\n\n" .
            "Silakan login ke panel admin untuk memverifikasi permohonan ini.";
    }
}
