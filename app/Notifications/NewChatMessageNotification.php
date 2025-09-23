<?php

namespace App\Notifications;

use App\Models\Chat;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Chat $chat;
    public Inquiry $inquiry;
    public User $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Chat $chat, Inquiry $inquiry, User $sender)
    {
        $this->chat = $chat;
        $this->inquiry = $inquiry;
        $this->sender = $sender;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = 'New Chat Message - Inquiry #' . $this->inquiry->id;
        $messageLine = $this->sender->name . ' sent a new message in Inquiry #' . $this->inquiry->id;
        
        // Add private message indicator
        if ($this->chat->recipient_id) {
            $subject = 'New Private Message - Inquiry #' . $this->inquiry->id;
            $messageLine = $this->sender->name . ' sent you a private message in Inquiry #' . $this->inquiry->id;
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line($messageLine)
                    ->line('Message: ' . $this->chat->message)
                    ->action('View Inquiry', route('dashboard.inquiries.show', $this->inquiry))
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
        $title = 'New Chat Message';
        $messageText = $this->sender->name . ' sent a message in Inquiry #' . $this->inquiry->id;
        $icon = 'fa-comments';
        $color = 'info';
        
        // Add private message indicators
        if ($this->chat->recipient_id) {
            $title = 'New Private Message';
            $messageText = $this->sender->name . ' sent you a private message in Inquiry #' . $this->inquiry->id;
            $icon = 'fa-lock';
            $color = 'warning';
        }

        return [
            'inquiry_id' => $this->inquiry->id,
            'inquiry_name' => $this->inquiry->name,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'recipient_id' => $this->chat->recipient_id,
            'message' => substr($this->chat->message, 0, 100) . '...',
            'chat_id' => $this->chat->id,
            'created_at' => $this->chat->created_at->toISOString(),
            'type' => 'new_chat_message',
            'title' => $title,
            'message_text' => $messageText,
            'action_url' => route('dashboard.inquiries.show', $this->inquiry),
            'icon' => $icon,
            'color' => $color,
            'is_private' => !is_null($this->chat->recipient_id)
        ];
    }
}