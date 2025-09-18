<?php

namespace App\Mail\Client;

use App\Models\BookingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public BookingFile $bookingFile;

    /**
     * Create a new message instance.
     */
    public function __construct(BookingFile $bookingFile)
    {
        $this->bookingFile = $bookingFile;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - ' . $this->bookingFile->file_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client.booking-confirmation',
            with: [
                'bookingFile' => $this->bookingFile,
                'client' => $this->bookingFile->inquiry->client,
                'inquiry' => $this->bookingFile->inquiry,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
