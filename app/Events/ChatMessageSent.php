<?php

namespace App\Events;

use App\Models\Chat;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Chat $chat;
    public Inquiry $inquiry;
    public User $sender;

    /**
     * Create a new event instance.
     */
    public function __construct(Chat $chat, Inquiry $inquiry, User $sender)
    {
        $this->chat = $chat;
        $this->inquiry = $inquiry;
        $this->sender = $sender;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('inquiry.' . $this->inquiry->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'chat' => $this->chat->load(['sender', 'recipient']),
            'inquiry_id' => $this->inquiry->id,
            'sender' => $this->sender,
            'recipient' => $this->chat->recipient,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
