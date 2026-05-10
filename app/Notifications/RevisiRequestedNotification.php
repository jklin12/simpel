<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanSurat;

class RevisiRequestedNotification extends Notification
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
            'title'         => 'Request Perubahan Surat',
            'message'       => "Admin Kelurahan mengajukan request perubahan untuk surat {$this->permohonan->jenisSurat->nama} (No. {$this->permohonan->nomor_surat}) atas nama {$this->permohonan->nama_pemohon}. Mohon ditinjau.",
            'type'          => 'warning',
            'url'           => '',
        ];
    }
}
