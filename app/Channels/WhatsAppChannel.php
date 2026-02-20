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

        $message = $notification->toWhatsApp($notifiable);
        $to = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone ?? $notifiable->phone_pemohon;

        if (!$to) {
            return; // No phone number found
        }

        // Placeholder for API Call
        // Example: 
        Http::post('http://wa.banjarbaru-bagawi.id/send-message', ['number' => $to, 'message' => $message]);

        \Log::info("WhatsApp sent to {$to}: {$message}");
    }
}
