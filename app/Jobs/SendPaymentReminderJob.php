<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\BookingFile;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use App\Services\Email\MailValidator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all unpaid payments that are older than 7 days
        $unpaidPayments = Payment::with(['booking.inquiry.client'])
            ->where('status', PaymentStatus::NOT_PAID)
            ->where('created_at', '<=', now()->subDays(7))
            ->where('created_at', '>=', now()->subDays(30)) // Don't send reminders for very old payments
            ->get();

        foreach ($unpaidPayments as $payment) {
            $this->sendPaymentReminder($payment);
        }

        // Get booking files with pending payments that are older than 14 days
        $bookingFilesWithPendingPayments = BookingFile::with(['inquiry.client', 'payments'])
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->where('status', '!=', BookingStatus::REFUNDED)
            ->where('status', '!=', BookingStatus::COMPLETED)
            ->where('created_at', '<=', now()->subDays(14))
            ->whereHas('payments', function ($query) {
                $query->where('status', PaymentStatus::NOT_PAID);
            })
            ->get();

        foreach ($bookingFilesWithPendingPayments as $bookingFile) {
            $this->sendBookingPaymentReminder($bookingFile);
        }

        Log::info('Payment reminder job completed', [
            'unpaid_payments_count' => $unpaidPayments->count(),
            'booking_files_with_pending_payments_count' => $bookingFilesWithPendingPayments->count(),
        ]);
    }

    /**
     * Send payment reminder for a specific payment
     */
    private function sendPaymentReminder(Payment $payment): void
    {
        try {
            $client = $payment->booking->inquiry->client;
            
            if (!$client || !MailValidator::isValid($client->email)) {
                Log::warning('Invalid email for payment reminder', [
                    'payment_id' => $payment->id,
                    'client_email' => $client->email ?? 'null',
                ]);
                return;
            }

            // Here you would send the actual email
            // For now, we'll just log it
            Log::info('Payment reminder sent', [
                'payment_id' => $payment->id,
                'client_email' => $client->email,
                'amount' => $payment->amount,
                'booking_file' => $payment->booking->file_name,
            ]);

            // Update last reminder sent timestamp (you might want to add this field to payments table)
            $payment->update(['updated_at' => now()]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment reminder', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send booking payment reminder for booking files with pending payments
     */
    private function sendBookingPaymentReminder(BookingFile $bookingFile): void
    {
        try {
            $client = $bookingFile->inquiry->client;
            
            if (!$client || !MailValidator::isValid($client->email)) {
                Log::warning('Invalid email for booking payment reminder', [
                    'booking_file_id' => $bookingFile->id,
                    'client_email' => $client->email ?? 'null',
                ]);
                return;
            }

            $pendingPayments = $bookingFile->payments()
                ->where('status', PaymentStatus::NOT_PAID)
                ->get();

            // Here you would send the actual email
            // For now, we'll just log it
            Log::info('Booking payment reminder sent', [
                'booking_file_id' => $bookingFile->id,
                'client_email' => $client->email,
                'pending_payments_count' => $pendingPayments->count(),
                'total_pending_amount' => $pendingPayments->sum('amount'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send booking payment reminder', [
                'booking_file_id' => $bookingFile->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

