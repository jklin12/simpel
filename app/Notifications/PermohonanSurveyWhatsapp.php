<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use App\Models\PermohonanSurat;
use App\Models\WhatsappNotificationLog;

/**
 * Ajakan isi survei kepuasan — dikirim dengan delay setelah surat selesai (completed).
 */
class PermohonanSurveyWhatsapp extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [60, 300, 600];
    public string $whatsappType = 'survey';

    public $permohonan;

    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    /**
     * Dicek saat job jalan (setelah delay): lewati jika surat ditarik ke revisi
     * atau survei sudah pernah terkirim (surat selesai ulang setelah revisi).
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        $p = $this->permohonan->fresh();

        if (!$p || $p->status !== 'completed') {
            return false;
        }

        return !WhatsappNotificationLog::where('permohonan_id', $p->id)
            ->where('notification_type', $this->whatsappType)
            ->where('status', 'sent')
            ->exists();
    }

    public function toWhatsApp(object $notifiable)
    {
        $p = $this->permohonan;

        return "Yth. Sdr. {$p->nama_pemohon}\n\n" .
            "Kami mengajak pian untuk berpartisipasi dalam Survei Penggunaan Sistem Informasi Manajemen Pelayanan Kecamatan Landasan Ulin (SiMPEL) sebagai bahan evaluasi dan peningkatan kualitas pelayanan publik kami.\n\n" .
            "Mohon kesediaannya mengisi kuesioner berikut.\n\n" .
            "🔗 Link Kuesioner:\n" .
            config('services.whatsapp.survey_url') . "\n\n" .
            "Masukan pian sangat bermanfaat untuk memperbaiki dan meningkatkan kualitas pelayanan bagi pian-pian sabarataan..\n\n" .
            "Terima kasih atas partisipasinya. 🙏";
    }
}
