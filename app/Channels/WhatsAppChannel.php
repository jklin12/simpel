<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            throw new \Exception('Notification is missing toWhatsApp method.');
        }

        $messageData = $notification->toWhatsApp($notifiable);

        if (is_array($messageData)) {
            $message = $messageData['message'] ?? '';
            $fileContent = $messageData['file_content'] ?? null;
            $filename = $messageData['filename'] ?? 'document.pdf';
            $customTo = $messageData['to'] ?? null;
        } else {
            $message = $messageData;
            $fileContent = null;
            $filename = null;
            $customTo = null;
        }

        $to = $customTo ?? $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone ?? $notifiable->phone_pemohon;

        if (!$to) {
            return; // No phone number found
        }

        // Override destination number for local development
        if (config('app.env') === 'local') {
            $to = '085600200913';
        }

        $baseUrl = config('services.whatsapp.base_url', 'http://127.0.0.1:5003');
        $username = config('services.whatsapp.username', '');
        $password = config('services.whatsapp.password', '');

        $client = Http::withBasicAuth($username, $password);

        if ($fileContent) {
            // Write temp file to attach
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
            file_put_contents($tempPath, $fileContent);

            $client->attach(
                'file',
                file_get_contents($tempPath),
                $filename
            )->post($baseUrl . '/send-media', [
                'number' => $to,
                'caption' => $message
            ]);

            @unlink($tempPath);
            \Illuminate\Support\Facades\Log::info("WhatsApp (Media) sent to {$to}");
        } else {
            $client->post($baseUrl . '/send-message', [
                'number' => $to,
                'message' => $message
            ]);
            \Illuminate\Support\Facades\Log::info("WhatsApp sent to {$to}: {$message}");
        }
    }
}
