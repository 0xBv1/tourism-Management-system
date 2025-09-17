<?php

namespace App\Notifications\Admin;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Inquiry $inquiry;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Inquiry Confirmed - #' . $this->inquiry->id)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('A new inquiry has been confirmed and requires your attention.')
                    ->line('**Inquiry Details:**')
                    ->line('• Customer: ' . $this->inquiry->name)
                    ->line('• Email: ' . $this->inquiry->email)
                    ->line('• Phone: ' . $this->inquiry->phone)
                    ->line('• Subject: ' . $this->inquiry->subject)
                    ->line('• Confirmed At: ' . $this->inquiry->confirmed_at->format('Y-m-d H:i:s'))
                    ->action('View Inquiry', route('dashboard.inquiries.show', $this->inquiry))
                    ->line('Please review the inquiry and take necessary actions.')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'inquiry_id' => $this->inquiry->id,
            'customer_name' => $this->inquiry->name,
            'subject' => $this->inquiry->subject,
            'confirmed_at' => $this->inquiry->confirmed_at,
            'message' => 'New inquiry #' . $this->inquiry->id . ' has been confirmed by ' . $this->inquiry->name,
        ];
    }
}
