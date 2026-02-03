<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

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

        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone ?? $notifiable->phone_pemohon;

        if (!$to) {
            return; // No phone number found
        }

        // Placeholder for API Call
        // Example: Http::post('https://api.whatsapp-provider.com/send', ['phone' => $to, 'message' => $message]);

        \Log::info("WhatsApp sent to {$to}: {$message}");
    }
}
