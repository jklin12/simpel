<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanSurat;

class RevisiRequestApprovedNotification extends Notification
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
        return [
            'permohonan_id' => $this->permohonan->id,
            'title'         => 'Request Perubahan Disetujui',
            'message'       => "Request perubahan Anda untuk surat {$this->permohonan->jenisSurat->nama} (No. {$this->permohonan->nomor_surat}) telah disetujui. Silakan edit data dan klik Konfirmasi Selesai Edit.",
            'type'          => 'success',
            'url'           => '',
        ];
    }
}
