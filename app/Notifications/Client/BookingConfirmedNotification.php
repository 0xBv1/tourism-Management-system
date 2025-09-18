<?php

namespace App\Notifications\Client;

use App\Models\BookingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public BookingFile $bookingFile;

    /**
     * Create a new notification instance.
     */
    public function __construct(BookingFile $bookingFile)
    {
        $this->bookingFile = $bookingFile;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
        // Add WhatsApp if phone number is available
        if ($notifiable->phone) {
            $channels[] = 'whatsapp';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed - ' . $this->bookingFile->file_name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed.')
            ->line('Booking File: ' . $this->bookingFile->file_name)
            ->line('Total Amount: ' . $this->bookingFile->currency . ' ' . number_format($this->bookingFile->total_amount, 2))
            ->line('Status: ' . $this->bookingFile->status->getLabel())
            ->action('View Booking', url('/bookings/' . $this->bookingFile->id))
            ->line('Thank you for choosing our services!');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        return [
            'body' => "Hello {$notifiable->name}! Your booking has been confirmed.\n\n" .
                     "Booking: {$this->bookingFile->file_name}\n" .
                     "Amount: {$this->bookingFile->currency} " . number_format($this->bookingFile->total_amount, 2) . "\n" .
                     "Status: {$this->bookingFile->status->getLabel()}\n\n" .
                     "Thank you for choosing our services!"
        ];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        return [
            'body' => "Booking confirmed: {$this->bookingFile->file_name}. " .
                     "Amount: {$this->bookingFile->currency} " . number_format($this->bookingFile->total_amount, 2) . ". " .
                     "Thank you!"
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'booking_confirmed',
            'booking_file_id' => $this->bookingFile->id,
            'booking_file_name' => $this->bookingFile->file_name,
            'amount' => $this->bookingFile->total_amount,
            'currency' => $this->bookingFile->currency,
            'status' => $this->bookingFile->status->value,
            'message' => 'Your booking has been confirmed.',
        ];
    }
}
