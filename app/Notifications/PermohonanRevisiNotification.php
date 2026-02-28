<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanSurat;

/**
 * Notifikasi database untuk admin kelurahan saat pemohon mengajukan revisi.
 * Dibedakan dari PermohonanBaruNotification agar admin tahu ini adalah revisi.
 */
class PermohonanRevisiNotification extends Notification
{
    use Queueable;

    public $permohonan;

    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $revisiKe = $this->permohonan->revision_count;

        return [
            'permohonan_id' => $this->permohonan->id,
            'title'         => "Revisi Permohonan (ke-{$revisiKe})",
            'message'       => "Revisi ke-{$revisiKe}: Permohonan {$this->permohonan->jenisSurat->nama} dari {$this->permohonan->nama_pemohon} telah diajukan ulang. Harap diverifikasi kembali.",
            'type'          => 'info',
            'url'           => '',
        ];
    }
}
