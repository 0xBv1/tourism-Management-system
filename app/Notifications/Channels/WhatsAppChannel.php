<?php

namespace App\Notifications\Channels;

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
        $message = $notification->toWhatsApp($notifiable);
        
        if (!$message) {
            return;
        }

        try {
            // Get WhatsApp configuration
            $whatsappApiUrl = config('services.whatsapp.api_url');
            $whatsappToken = config('services.whatsapp.token');
            $whatsappPhoneNumberId = config('services.whatsapp.phone_number_id');
            
            if (!$whatsappApiUrl || !$whatsappToken || !$whatsappPhoneNumberId) {
                Log::warning('WhatsApp configuration missing');
                return;
            }

            // Send WhatsApp message
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $whatsappToken,
                'Content-Type' => 'application/json',
            ])->post($whatsappApiUrl . '/' . $whatsappPhoneNumberId . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => $notifiable->phone,
                'type' => 'text',
                'text' => [
                    'body' => $message['body']
                ]
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $notifiable->phone,
                    'message_id' => $response->json('messages.0.id')
                ]);
            } else {
                Log::error('WhatsApp message failed', [
                    'to' => $notifiable->phone,
                    'error' => $response->json()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp channel error', [
                'to' => $notifiable->phone,
                'error' => $e->getMessage()
            ]);
        }
    }
}
