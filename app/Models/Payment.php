<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'booking_id',
        'gateway',
        'amount',
        'status',
        'paid_at',
        'transaction_request',
        'transaction_verification',
        'notes',
        'reference_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'transaction_request' => 'array',
        'transaction_verification' => 'array',
        'status' => PaymentStatus::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class, 'booking_id');
    }

    public function scopePaid($query)
    {
        return $query->where('status', PaymentStatus::PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::PENDING);
    }

    public function scopeNotPaid($query)
    {
        return $query->where('status', PaymentStatus::NOT_PAID);
    }

    public function scopeByGateway($query, string $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            PaymentStatus::PAID => 'success',
            PaymentStatus::PENDING => 'warning',
            PaymentStatus::NOT_PAID => 'danger',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            PaymentStatus::PAID => 'Paid',
            PaymentStatus::PENDING => 'Pending',
            PaymentStatus::NOT_PAID => 'Not Paid',
        };
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->booking->currency . ' ' . number_format($this->amount, 2);
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::PAID;
    }

    public function markAsPaid(): void
    {
        $this->update([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }
}
