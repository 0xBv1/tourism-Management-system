<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSms($notifiable);
        
        if (!$message) {
            return;
        }

        try {
            // Get SMS configuration (using Twilio as example)
            $twilioSid = config('services.twilio.sid');
            $twilioToken = config('services.twilio.token');
            $twilioFrom = config('services.twilio.from');
            
            if (!$twilioSid || !$twilioToken || !$twilioFrom) {
                Log::warning('SMS configuration missing');
                return;
            }

            // Send SMS via Twilio
            $response = Http::withBasicAuth($twilioSid, $twilioToken)
                ->asForm()
                ->post('https://api.twilio.com/2010-04-01/Accounts/' . $twilioSid . '/Messages.json', [
                    'From' => $twilioFrom,
                    'To' => $notifiable->phone,
                    'Body' => $message['body']
                ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'to' => $notifiable->phone,
                    'message_sid' => $response->json('sid')
                ]);
            } else {
                Log::error('SMS failed', [
                    'to' => $notifiable->phone,
                    'error' => $response->json()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SMS channel error', [
                'to' => $notifiable->phone,
                'error' => $e->getMessage()
            ]);
        }
    }
}
