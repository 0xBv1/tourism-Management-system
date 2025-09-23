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
            $users = collect();

            // Handle private messages
            if ($event->chat->recipient_id) {
                // For private messages, only notify the recipient
                $recipient = User::find($event->chat->recipient_id);
                if ($recipient && $recipient->id !== $event->sender->id) {
                    $users->push($recipient);
                }
            } else {
                // For public messages, notify all relevant users
                $users = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
                })->where('id', '!=', $event->sender->id)->get();
            }

            // Send notification to relevant users
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
