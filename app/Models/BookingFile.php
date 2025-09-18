<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'file_name',
        'file_path',
        'status',
        'generated_at',
        'sent_at',
        'downloaded_at',
        'checklist',
        'notes',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'status' => BookingStatus::class,
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'checklist' => 'array',
        'total_amount' => 'decimal:2',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id');
    }

    public function resourceBookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', BookingStatus::PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', BookingStatus::CONFIRMED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', BookingStatus::IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', BookingStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', BookingStatus::CANCELLED);
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', BookingStatus::REFUNDED);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount') ?? 0;
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->total_paid;
    }

    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    public function updateChecklistItem(string $item, bool $completed): void
    {
        $checklist = $this->checklist ?? [];
        $checklist[$item] = $completed;
        $this->update(['checklist' => $checklist]);
    }

    public function getChecklistProgressAttribute(): int
    {
        if (empty($this->checklist)) {
            return 0;
        }

        $completed = array_filter($this->checklist);
        return (int) round((count($completed) / count($this->checklist)) * 100);
    }
}
