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
        'inquiry_id',
        'guest_name',
        'email',
        'phone',
        'arrival_date',
        'departure_date',
        'number_pax',
        'tour_name',
        'nationality',
        'subject',
        'status',
        'client_id',
        'assigned_to',
        'assigned_reservation_id',
        'assigned_operator_id',
        'assigned_admin_id',
        'booking_file_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => InquiryStatus::class,
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedReservation(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_reservation_id');
    }

    public function assignedOperator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_operator_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    public function syncBookingFileData(): void
    {
        if ($this->bookingFile) {
            $this->bookingFile->update([
                'total_amount' => $this->total_amount,
                'currency' => 'USD', // Default currency, can be made configurable
            ]);
        }
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class)->orderBy('created_at');
    }

    /**
     * Get all assigned users for this inquiry
     */
    public function getAllAssignedUsers(): array
    {
        $assignedUsers = [];
        
        if ($this->assignedUser) {
            $assignedUsers[] = [
                'user' => $this->assignedUser,
                'role' => 'General',
                'field' => 'assigned_to'
            ];
        }
        
        if ($this->assignedReservation) {
            $assignedUsers[] = [
                'user' => $this->assignedReservation,
                'role' => 'Reservation',
                'field' => 'assigned_reservation_id'
            ];
        }
        
        if ($this->assignedOperator) {
            $assignedUsers[] = [
                'user' => $this->assignedOperator,
                'role' => 'Operator',
                'field' => 'assigned_operator_id'
            ];
        }
        
        if ($this->assignedAdmin) {
            $assignedUsers[] = [
                'user' => $this->assignedAdmin,
                'role' => 'Admin',
                'field' => 'assigned_admin_id'
            ];
        }
        
        return $assignedUsers;
    }

    /**
     * Check if a user is assigned to this inquiry in any role
     */
    public function isAssignedToUser(User $user): bool
    {
        return $this->assigned_to === $user->id ||
               $this->assigned_reservation_id === $user->id ||
               $this->assigned_operator_id === $user->id ||
               $this->assigned_admin_id === $user->id;
    }

    /**
     * Get the role of a specific user for this inquiry
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->assigned_to === $user->id) return 'General';
        if ($this->assigned_reservation_id === $user->id) return 'Reservation';
        if ($this->assigned_operator_id === $user->id) return 'Operator';
        if ($this->assigned_admin_id === $user->id) return 'Admin';
        
        return null;
    }

    public function scopePending($query)
    {
        return $query->where('status', InquiryStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', InquiryStatus::CONFIRMED);
    }


    public function scopeCancelled($query)
    {
        return $query->where('status', InquiryStatus::CANCELLED);
    }

    /**
     * Generate custom inquiry ID
     */
    public function generateInquiryId(): string
    {
        $guestName = str_replace(' ', '', $this->guest_name);
        $nationality = str_replace(' ', '', $this->nationality);
        return "Inquiry #{$this->id}.{$guestName}.{$nationality}";
    }

    /**
     * Calculate remaining amount
     */
    public function calculateRemainingAmount(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Boot method to set inquiry_id and calculate remaining amount
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($inquiry) {
            if (empty($inquiry->inquiry_id)) {
                $inquiry->update(['inquiry_id' => $inquiry->generateInquiryId()]);
            }
        });

        static::updating(function ($inquiry) {
            if ($inquiry->total_amount && $inquiry->paid_amount) {
                $inquiry->remaining_amount = $inquiry->calculateRemainingAmount();
            }
        });
    }
}
