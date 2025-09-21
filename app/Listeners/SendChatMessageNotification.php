<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use App\Models\User;
use App\Notifications\NewChatMessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChatMessageNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ChatMessageSent  $event
     * @return void
     */
    public function handle(ChatMessageSent $event)
    {
        try {
            // Get all users who have access to this inquiry (Sales and Reservation/Operation roles)
            $users = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
            })->where('id', '!=', $event->sender->id)->get();

            // Send notification to all relevant users
            foreach ($users as $user) {
                try {
                    $user->notify(new NewChatMessageNotification(
                        $event->chat,
                        $event->inquiry,
                        $event->sender
                    ));
                } catch (\Exception $e) {
                    // Log the error but continue with other users
                    \Log::error("Failed to send chat notification to user {$user->id}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the job
            \Log::error('Failed to process chat message notification: ' . $e->getMessage());
        }
    }
}
