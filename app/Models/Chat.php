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
        'message',
        'visibility',
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
     * Scope a query to only include messages visible to the current user.
     */
    public function scopeVisibleTo($query, User $user)
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return $query->where(function($q) use ($userRoles) {
            // Admin can see all messages
            if (in_array('Admin', $userRoles) || in_array('Administrator', $userRoles)) {
                return $q; // No additional filtering for admin
            }
            
            // Regular users can see messages based on visibility
            $q->where('visibility', 'all'); // Everyone can see 'all' messages
            
            // Add role-specific visibility
            foreach ($userRoles as $role) {
                $q->orWhere('visibility', strtolower($role));
            }
        });
    }

    /**
     * Determine if a message is visible to a specific user.
     */
    public function isVisibleTo(User $user): bool
    {
        $userRoles = $user->roles->pluck('name')->toArray();
        
        // Admin can see all messages
        if (in_array('Admin', $userRoles) || in_array('Administrator', $userRoles)) {
            return true;
        }
        
        // Check if message is visible to user's roles
        if ($this->visibility === 'all') {
            return true;
        }
        
        foreach ($userRoles as $role) {
            if ($this->visibility === strtolower($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get the visibility display name.
     */
    public function getVisibilityDisplayAttribute(): string
    {
        return match($this->visibility) {
            'all' => 'Everyone',
            'reservation' => 'Reservation Only',
            'operation' => 'Operation Only',
            'admin' => 'Admin Only',
            default => ucfirst($this->visibility)
        };
    }
}
