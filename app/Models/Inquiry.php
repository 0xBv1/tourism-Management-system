<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_notes',
        'client_id',
        'assigned_to',
        'booking_file_id',
        'confirmed_at',
        'completed_at',
        'user_confirmations',
        'user1_confirmed_at',
        'user2_confirmed_at',
        'user1_id',
        'user2_id',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'user_confirmations' => 'array',
        'user1_confirmed_at' => 'datetime',
        'user2_confirmed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class)->orderBy('created_at');
    }

    public function scopePending($query)
    {
        return $query->where('status', InquiryStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', InquiryStatus::CONFIRMED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', InquiryStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', InquiryStatus::CANCELLED);
    }

    /**
     * Check if both users have confirmed
     */
    public function isFullyConfirmed(): bool
    {
        return $this->user1_confirmed_at !== null && $this->user2_confirmed_at !== null;
    }

    /**
     * Check if a specific user has confirmed
     */
    public function hasUserConfirmed(int $userId): bool
    {
        if ($this->user1_id === $userId) {
            return $this->user1_confirmed_at !== null;
        }
        
        if ($this->user2_id === $userId) {
            return $this->user2_confirmed_at !== null;
        }
        
        return false;
    }

    /**
     * Confirm by a specific user
     */
    public function confirmByUser(int $userId): bool
    {
        if ($this->user1_id === $userId) {
            $this->update(['user1_confirmed_at' => now()]);
            return true;
        }
        
        if ($this->user2_id === $userId) {
            $this->update(['user2_confirmed_at' => now()]);
            return true;
        }
        
        return false;
    }

    /**
     * Get confirmation status for display
     */
    public function getConfirmationStatus(): array
    {
        return [
            'user1_confirmed' => $this->user1_confirmed_at !== null,
            'user2_confirmed' => $this->user2_confirmed_at !== null,
            'fully_confirmed' => $this->isFullyConfirmed(),
            'user1_name' => $this->user1?->name ?? 'User 1',
            'user2_name' => $this->user2?->name ?? 'User 2',
            'user1_confirmed_at' => $this->user1_confirmed_at,
            'user2_confirmed_at' => $this->user2_confirmed_at,
        ];
    }
}
