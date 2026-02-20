<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;

class PermohonanApprovedWhatsapp extends Notification
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

    public function toWhatsApp(object $notifiable)
    {
        $p = $this->permohonan;

        // Jika sudah completed (final approval)
        if ($p->status === 'completed') {
            return "Halo {$p->nama_pemohon},\n\n" .
                "✅ Permohonan surat *{$p->jenisSurat->nama}* Anda telah *SELESAI* diproses.\n\n" .
                "Nomor Surat: *{$p->nomor_surat}*\n" .
                "Tanggal Surat: {$p->tanggal_surat->format('d/m/Y')}\n\n" .
                "Silakan datang ke kantor kelurahan untuk mengambil surat Anda.\n\n" .
                "Kode Tracking: *{$p->track_token}*\n" .
                "Terima kasih.";
        }

        // Jika masih in_review (approved di step tertentu, lanjut ke step berikutnya)
        return "Halo {$p->nama_pemohon},\n\n" .
            "📋 Permohonan surat *{$p->jenisSurat->nama}* Anda telah *DISETUJUI* pada tahap verifikasi saat ini.\n\n" .
            "Permohonan Anda sedang dilanjutkan ke tahap berikutnya.\n\n" .
            "Kode Tracking: *{$p->track_token}*\n" .
            "Terima kasih atas kesabarannya.";
    }
}
