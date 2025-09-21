<?php

namespace App\Listeners;

use App\Events\NewInquiryCreated;
use App\Models\User;
use App\Notifications\NewInquiryNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewInquiryNotification implements ShouldQueue
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
    public function handle(NewInquiryCreated $event): void
    {
        try {
            // Get all users who should be notified about new inquiries
            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
            })->get();

            // Send notification to all relevant users
            foreach ($users as $user) {
                try {
                    $user->notify(new NewInquiryNotification($event->inquiry));
                } catch (\Exception $e) {
                    // Log the error but continue with other users
                    \Log::error("Failed to send new inquiry notification to user {$user->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the job
            \Log::error('Failed to process new inquiry notification: ' . $e->getMessage());
        }
    }
}
