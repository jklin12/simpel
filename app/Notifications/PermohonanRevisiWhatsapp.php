<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanRevisiWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'revisi';

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
            "Pantau status permohonan Anda di sini:\n" .
            route('layanan.surat.tracking.search', ['track_token' => $this->permohonan->track_token]) . "\n\n" .
            "Permohonan Anda sedang dalam antrian verifikasi kembali oleh petugas kelurahan.\n" .
            "Terima kasih.";
    }
}
