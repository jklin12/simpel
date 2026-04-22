<?php

namespace App\Channels;

use App\Models\PermohonanSurat;
use App\Models\WhatsappNotificationLog;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        // Create log entry
        $permohonanId = $notifiable instanceof PermohonanSurat ? $notifiable->id : null;
        $notificationType = $notification->whatsappType ?? 'unknown';
        $messagePreview = substr(str_replace("\n", ' ', $message), 0, 200);

        $log = WhatsappNotificationLog::create([
            'permohonan_id' => $permohonanId,
            'notification_type' => $notificationType,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'phone_to' => $to,
            'message_preview' => $messagePreview,
            'has_file' => (bool) $fileContent,
            'status' => 'pending',
        ]);

        $baseUrl = config('services.whatsapp.base_url', 'http://127.0.0.1:5003');
        $username = config('services.whatsapp.username', '');
        $password = config('services.whatsapp.password', '');

        $client = Http::withBasicAuth($username, $password);

        try {
            if ($fileContent) {
                // Write temp file to attach
                $tempPath = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
                file_put_contents($tempPath, $fileContent);

                $response = $client->attach(
                    'file',
                    file_get_contents($tempPath),
                    $filename
                )->post($baseUrl . '/send-media', [
                    'number' => $to,
                    'caption' => $message
                ]);

                @unlink($tempPath);
            } else {
                $response = $client->post($baseUrl . '/send-message', [
                    'number' => $to,
                    'message' => $message
                ]);
            }

            // Check response status
            if (!$response->successful() || $response->json('status') === 'false') {
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'API returned failure: ' . $response->body(),
                    'response_code' => $response->status(),
                ]);

                Log::error("WhatsApp send failed to {$to}: " . $response->body());
                throw new \Exception('WhatsApp API returned failure');
            }

            $log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("WhatsApp sent to {$to}" . ($fileContent ? ' (with media)' : ''));
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error("WhatsApp send exception: " . $e->getMessage());
            throw $e;
        }
    }
}
