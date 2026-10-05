<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanKelurahanLog;

/**
 * Notifikasi internal (database) ke admin saat permohonan dipindahkan wilayahnya.
 * Tidak dikirim ke pemohon.
 */
class PermohonanPindahWilayahNotification extends Notification
{
    use Queueable;

    public function __construct(public PermohonanKelurahanLog $log)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $permohonan = $this->log->permohonan;

        return [
            'type'            => 'pindah_wilayah',
            'permohonan_id'   => $permohonan->id,
            'nomor_permohonan' => $permohonan->nomor_permohonan,
            'dari_kelurahan'  => $this->log->fromKelurahan->nama ?? '-',
            'ke_kelurahan'    => $this->log->toKelurahan->nama ?? '-',
            'alasan'          => $this->log->alasan,
            'message'         => "Permohonan {$permohonan->nomor_permohonan} dipindahkan dari "
                . ($this->log->fromKelurahan->nama ?? '-') . " ke " . ($this->log->toKelurahan->nama ?? '-') . '.',
        ];
    }
}
