<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->hasAnyRole(['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Chat $chat)
    {
        // Admin and Administrator can see all messages
        if ($user->hasAnyRole(['Admin', 'Administrator'])) {
            return true;
        }

        // Users can only see messages they sent or received
        return $chat->sender_id === $user->id || $chat->recipient_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->hasAnyRole(['Sales', 'Reservation', 'Operation', 'Admin', 'Administrator']);
    }

    /**
     * Determine whether the user can send messages to a specific recipient.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $recipient
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function sendTo(User $user, User $recipient = null)
    {
        if (!$recipient) {
            return false;
        }

        // Admin and Administrator can send to anyone
        if ($user->hasAnyRole(['Admin', 'Administrator'])) {
            return true;
        }

        // Sales can send to Reservation and Operation users ONLY
        if ($user->hasRole('Sales')) {
            return $recipient->hasAnyRole(['Reservation', 'Operation']);
        }

        // Reservation and Operation can only send to Sales users
        if ($user->hasAnyRole(['Reservation', 'Operation'])) {
            return $recipient->hasRole('Sales');
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Chat $chat)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Chat $chat)
    {
        //
    }
}
