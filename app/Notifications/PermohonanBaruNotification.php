<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PermohonanSurat;

class PermohonanBaruNotification extends Notification
{
    use Queueable;

    public $permohonan;

    /**
     * Create a new notification instance.
     */
    public function __construct(PermohonanSurat $permohonan)
    {
        $this->permohonan = $permohonan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'permohonan_id' => $this->permohonan->id,
            'title' => 'Permohonan Baru Masuk',
            'message' => 'Permohonan ' . $this->permohonan->jenisSurat->nama . ' baru dari ' . $this->permohonan->nama_pemohon,
            'type' => 'warning',
            'url' => '', //route('approval.index'), // Assuming route, or use helper
        ];
    }
}
