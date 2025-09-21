<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInquiryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Inquiry $inquiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Inquiry Received - #' . $this->inquiry->id)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new inquiry has been received from ' . $this->inquiry->name)
            ->line('Subject: ' . $this->inquiry->subject)
            ->line('Message: ' . substr($this->inquiry->message, 0, 100) . '...')
            ->action('View Inquiry', route('dashboard.inquiries.show', $this->inquiry))
            ->line('Please respond to this inquiry as soon as possible.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inquiry_id' => $this->inquiry->id,
            'inquiry_name' => $this->inquiry->name,
            'inquiry_subject' => $this->inquiry->subject,
            'inquiry_message' => substr($this->inquiry->message, 0, 100) . '...',
            'inquiry_status' => $this->inquiry->status->value,
            'inquiry_created_at' => $this->inquiry->created_at->toISOString(),
            'type' => 'new_inquiry',
            'title' => 'New Inquiry Received',
            'message' => 'New inquiry from ' . $this->inquiry->name . ' - ' . $this->inquiry->subject,
            'action_url' => route('dashboard.inquiries.show', $this->inquiry),
            'icon' => 'fa-envelope',
            'color' => 'primary'
        ];
    }
}
