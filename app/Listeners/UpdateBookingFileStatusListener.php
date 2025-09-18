<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use App\Models\BookingFile;
use App\Enums\BookingStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateBookingFileStatusListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;
        $bookingFile = $payment->booking;

        if (!$bookingFile) {
            return;
        }

        // Check if the booking is now fully paid
        if ($bookingFile->isFullyPaid()) {
            // Update status based on current status
            if ($bookingFile->status === BookingStatus::PENDING) {
                $bookingFile->update(['status' => BookingStatus::CONFIRMED]);
            } elseif ($bookingFile->status === BookingStatus::CONFIRMED) {
                $bookingFile->update(['status' => BookingStatus::IN_PROGRESS]);
            }
        }

        // Log the payment received (payment status is tracked through the payments relationship)

        // Log the payment received
        activity()
            ->performedOn($bookingFile)
            ->causedBy(auth()->user())
            ->withProperties([
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'gateway' => $payment->gateway,
                'status' => $payment->status->value,
            ])
            ->log('Payment received');
    }
}

