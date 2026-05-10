<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class RevisiRequestRejectedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'revisi_rejected';

    public $permohonan;
    public $catatan;

    public function __construct(PermohonanSurat $permohonan, string $catatan = '')
    {
        $this->permohonan = $permohonan;
        $this->catatan = $catatan;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable)
    {
        $message = "Halo {$notifiable->name},\n\n" .
            "❌ Request perubahan Anda untuk surat telah *DITOLAK*:\n\n" .
            "Jenis Surat: *{$this->permohonan->jenisSurat->nama}*\n" .
            "Nomor Surat: {$this->permohonan->nomor_surat}\n" .
            "Pemohon: {$this->permohonan->nama_pemohon}\n\n" .
            "Surat tetap dalam status Disetujui.\n\n";

        if ($this->catatan) {
            $message .= "Catatan:\n\"{$this->catatan}\"\n\n";
        }

        $message .= "Silakan login ke sistem untuk informasi lebih lanjut.\n\n" .
            "Terima kasih.";

        return $message;
    }
}
