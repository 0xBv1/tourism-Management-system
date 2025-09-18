<?php

namespace App\Mail\Client;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class MonthlyStatementMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Client $client;
    public Carbon $startDate;
    public Carbon $endDate;
    public array $statementData;

    /**
     * Create a new message instance.
     */
    public function __construct(Client $client, Carbon $startDate, Carbon $endDate, array $statementData)
    {
        $this->client = $client;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->statementData = $statementData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Monthly Statement - ' . $this->startDate->format('F Y'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.client.monthly-statement',
            with: [
                'client' => $this->client,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'statementData' => $this->statementData,
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
