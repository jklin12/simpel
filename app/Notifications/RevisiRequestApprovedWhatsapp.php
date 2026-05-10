<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class RevisiRequestApprovedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'revisi_approved';

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
        return "Halo {$notifiable->name},\n\n" .
            "✅ Request perubahan Anda untuk surat telah *DISETUJUI*:\n\n" .
            "Jenis Surat: *{$this->permohonan->jenisSurat->nama}*\n" .
            "Nomor Surat: {$this->permohonan->nomor_surat}\n" .
            "Pemohon: {$this->permohonan->nama_pemohon}\n\n" .
            "Silakan login ke sistem, edit data sesuai kebutuhan, kemudian klik \"Konfirmasi Selesai Edit\" untuk menyelesaikan proses revisi.\n\n" .
            "Terima kasih.";
    }
}
