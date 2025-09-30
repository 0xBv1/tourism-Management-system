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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference_number)) {
                $payment->reference_number = $payment->generateReferenceNumber();
            }
        });
    }

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
        $currency = $this->booking?->currency ?? 'USD';
        return $currency . ' ' . number_format($this->amount, 2);
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
        
        // Sync with booking file if exists
        if ($this->booking) {
            $this->booking->syncPaymentData();
        }
    }

    /**
     * Generate a unique reference number for the payment
     */
    public function generateReferenceNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        
        // Get the last payment reference number for today
        $lastPayment = static::whereDate('created_at', now()->toDateString())
            ->whereNotNull('reference_number')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPayment && $lastPayment->reference_number) {
            // Extract the sequence number from the last reference
            $lastRef = $lastPayment->reference_number;
            if (preg_match('/PAY-' . $date . '-(\d+)/', $lastRef, $matches)) {
                $sequence = (int) $matches[1] + 1;
            } else {
                $sequence = 1;
            }
        } else {
            $sequence = 1;
        }
        
        // Format sequence with leading zeros (4 digits)
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequenceFormatted;
    }

    /**
     * Get formatted reference number with prefix
     */
    public function getFormattedReferenceNumberAttribute(): string
    {
        return $this->reference_number ? 'REF: ' . $this->reference_number : 'N/A';
    }
}
