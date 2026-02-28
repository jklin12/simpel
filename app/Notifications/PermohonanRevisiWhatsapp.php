<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

/**
 * WhatsApp notification ke pemohon saat revisi berhasil diajukan ulang.
 */
class PermohonanRevisiWhatsapp extends Notification
{
    use Queueable;

    public $permohonan;

    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable): string
    {
        $revisiKe = $this->permohonan->revision_count;

        return "Halo {$this->permohonan->nama_pemohon},\n\n" .
            "Revisi permohonan *{$this->permohonan->jenisSurat->nama}* Anda (Revisi ke-{$revisiKe}) telah berhasil diajukan ulang.\n\n" .
            "Kode Tracking: *{$this->permohonan->track_token}*\n\n" .
            "Permohonan Anda sedang dalam antrian verifikasi kembali oleh petugas kelurahan.\n" .
            "Gunakan kode tracking di atas untuk memantau status terbaru.\n\n" .
            "Terima kasih.";
    }
}
