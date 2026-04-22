<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanRejectedWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'rejected';

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
            "❌ Mohon maaf, permohonan surat *{$p->jenisSurat->nama}* Anda *BELUM DAPAT DISETUJUI*.\n\n" .
            "Alasan: {$this->reason}\n\n" .
            "Silakan perbaiki data dan ajukan kembali melalui link berikut:\n" .
            route('layanan.surat.tracking.search', ['track_token' => $p->track_token]) . "\n\n" .
            "Atau hubungi kantor kelurahan untuk informasi lebih lanjut.\n\n" .
            "Terima kasih.";
    }
}
