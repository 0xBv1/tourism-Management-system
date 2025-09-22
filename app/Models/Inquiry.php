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
