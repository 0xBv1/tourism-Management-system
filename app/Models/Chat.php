<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'sender_id',
        'recipient_id',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the inquiry that owns the chat message.
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the user that sent the chat message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user that received the chat message.
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope a query to only include unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope a query to only include read messages.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark the message as read.
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Scope a query to only include messages visible to a specific user based on role.
     */
    public function scopeVisibleTo($query, User $user)
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        if (in_array('Sales', $userRoles)) {
            // Sales can see:
            // 1. Messages they sent (to anyone)
            // 2. Messages sent to them (from Reservation/Operation)
            return $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
            });
        } elseif (in_array('Reservation', $userRoles)) {
            // Reservation can only see:
            // 1. Messages they sent to Sales
            // 2. Messages Sales sent to them specifically
            return $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('recipient_id', $user->id)
                           ->whereHas('sender', function($senderQ) {
                               $senderQ->role('Sales');
                           });
                  });
            });
        } elseif (in_array('Operation', $userRoles)) {
            // Operation can only see:
            // 1. Messages they sent to Sales
            // 2. Messages Sales sent to them specifically
            return $query->where(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->orWhere(function($subQ) use ($user) {
                      $subQ->where('recipient_id', $user->id)
                           ->whereHas('sender', function($senderQ) {
                               $senderQ->role('Sales');
                           });
                  });
            });
        } else {
            // Admin and other roles can see all messages
            return $query;
        }
    }

    /**
     * Scope a query to only include public messages (no recipient specified).
     */
    public function scopePublic($query)
    {
        return $query->whereNull('recipient_id');
    }

    /**
     * Scope a query to only include private messages (recipient specified).
     */
    public function scopePrivate($query)
    {
        return $query->whereNotNull('recipient_id');
    }
}
