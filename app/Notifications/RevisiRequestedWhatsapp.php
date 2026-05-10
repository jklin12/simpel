<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class RevisiRequestedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'revisi_requested';

    public $permohonan;
    public $alasan;

    public function __construct(PermohonanSurat $permohonan, string $alasan)
    {
        $this->permohonan = $permohonan;
        $this->alasan = $alasan;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable)
    {
        return "Halo {$notifiable->name},\n\n" .
            "📝 Ada request perubahan baru untuk surat:\n\n" .
            "Jenis Surat: *{$this->permohonan->jenisSurat->nama}*\n" .
            "Pemohon: {$this->permohonan->nama_pemohon}\n" .
            "Nomor Surat: {$this->permohonan->nomor_surat}\n\n" .
            "Alasan Perubahan:\n" .
            "\"{$this->alasan}\"\n\n" .
            "Silakan login ke sistem untuk review dan approve/reject request ini.\n\n" .
            "Terima kasih.";
    }
}
