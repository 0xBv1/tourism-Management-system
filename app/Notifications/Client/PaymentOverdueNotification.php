<?php

namespace App\Notifications\Client;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Payment $payment;
    public int $daysOverdue;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, int $daysOverdue = 0)
    {
        $this->payment = $payment;
        $this->daysOverdue = $daysOverdue;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        
        // Add WhatsApp and SMS if phone number is available
        if ($notifiable->phone) {
            $channels[] = 'whatsapp';
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Payment Overdue - ' . ($this->payment->reference_number ?? '#' . $this->payment->id))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We noticed that you have an overdue payment.')
            ->line('Payment Reference: ' . ($this->payment->reference_number ?? '#' . $this->payment->id))
            ->line('Amount: ' . $this->payment->formatted_amount)
            ->line('Days Overdue: ' . $this->daysOverdue)
            ->line('Due Date: ' . $this->payment->created_at->addDays(30)->format('F d, Y'));

        if ($this->daysOverdue > 30) {
            $message->line('This payment is significantly overdue. Please contact us immediately to resolve this matter.');
        } elseif ($this->daysOverdue > 14) {
            $message->line('This payment is overdue. Please make payment as soon as possible.');
        } else {
            $message->line('This payment is approaching its due date. Please make payment soon.');
        }

        return $message
            ->action('Make Payment', url('/payments/' . $this->payment->id))
            ->line('If you have already made this payment, please ignore this notification.');
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $message = "Hello {$notifiable->name}! You have an overdue payment.\n\n" .
                  "Reference: " . ($this->payment->reference_number ?? '#' . $this->payment->id) . "\n" .
                  "Amount: {$this->payment->formatted_amount}\n" .
                  "Days Overdue: {$this->daysOverdue}\n\n";

        if ($this->daysOverdue > 30) {
            $message .= "This payment is significantly overdue. Please contact us immediately.";
        } elseif ($this->daysOverdue > 14) {
            $message .= "This payment is overdue. Please make payment as soon as possible.";
        } else {
            $message .= "This payment is approaching its due date. Please make payment soon.";
        }

        return ['body' => $message];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $message = "Overdue payment: " . ($this->payment->reference_number ?? '#' . $this->payment->id) . 
                  " - {$this->payment->formatted_amount} ({$this->daysOverdue} days overdue)";

        return ['body' => $message];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_overdue',
            'payment_id' => $this->payment->id,
            'reference_number' => $this->payment->reference_number,
            'amount' => $this->payment->amount,
            'currency' => $this->payment->booking->currency,
            'days_overdue' => $this->daysOverdue,
            'due_date' => $this->payment->created_at->addDays(30)->format('Y-m-d'),
            'message' => 'You have an overdue payment of ' . $this->payment->formatted_amount,
        ];
    }
}
