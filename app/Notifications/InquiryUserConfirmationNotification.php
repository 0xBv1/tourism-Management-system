<?php

namespace App\Notifications;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryUserConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Inquiry $inquiry;
    public User $confirmingUser;
    public bool $isFullyConfirmed;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inquiry $inquiry, User $confirmingUser, bool $isFullyConfirmed = false)
    {
        $this->inquiry = $inquiry;
        $this->confirmingUser = $confirmingUser;
        $this->isFullyConfirmed = $isFullyConfirmed;
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
        if ($this->isFullyConfirmed) {
            return (new MailMessage)
                ->subject('Inquiry Fully Confirmed - #' . $this->inquiry->id)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('The inquiry #' . $this->inquiry->id . ' has been fully confirmed by both users.')
                ->line('Confirmed by: ' . $this->confirmingUser->name)
                ->line('Subject: ' . $this->inquiry->subject)
                ->action('View Inquiry', route('dashboard.inquiries.show', $this->inquiry))
                ->line('The inquiry is now ready for processing.')
                ->line('Thank you for using our application!');
        } else {
            return (new MailMessage)
                ->subject('Inquiry User Confirmation - #' . $this->inquiry->id)
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line($this->confirmingUser->name . ' has confirmed inquiry #' . $this->inquiry->id)
                ->line('Subject: ' . $this->inquiry->subject)
                ->line('Waiting for the other user to confirm before the inquiry is fully confirmed.')
                ->action('View Inquiry', route('dashboard.inquiries.show', $this->inquiry))
                ->line('Thank you for using our application!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->isFullyConfirmed) {
            return [
                'inquiry_id' => $this->inquiry->id,
                'inquiry_name' => $this->inquiry->name,
                'inquiry_subject' => $this->inquiry->subject,
                'confirming_user_id' => $this->confirmingUser->id,
                'confirming_user_name' => $this->confirmingUser->name,
                'type' => 'inquiry_fully_confirmed',
                'title' => 'Inquiry Fully Confirmed',
                'message_text' => 'Inquiry #' . $this->inquiry->id . ' has been fully confirmed by both users',
                'action_url' => route('dashboard.inquiries.show', $this->inquiry),
                'icon' => 'fa-check-circle',
                'color' => 'success'
            ];
        } else {
            return [
                'inquiry_id' => $this->inquiry->id,
                'inquiry_name' => $this->inquiry->name,
                'inquiry_subject' => $this->inquiry->subject,
                'confirming_user_id' => $this->confirmingUser->id,
                'confirming_user_name' => $this->confirmingUser->name,
                'type' => 'inquiry_user_confirmation',
                'title' => 'User Confirmation Received',
                'message_text' => $this->confirmingUser->name . ' confirmed inquiry #' . $this->inquiry->id,
                'action_url' => route('dashboard.inquiries.show', $this->inquiry),
                'icon' => 'fa-user-check',
                'color' => 'info'
            ];
        }
    }
}
