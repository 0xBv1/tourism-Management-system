<?php

namespace App\Models;

use App\Enums\SettlementStatus;
use App\Enums\SettlementType;
use App\Enums\CommissionType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Settlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_number',
        'settlement_type',
        'resource_type',
        'resource_id',
        'month',
        'year',
        'start_date',
        'end_date',
        'total_bookings',
        'total_hours',
        'total_days',
        'total_amount',
        'commission_type',
        'commission_value',
        'commission_amount',
        'tax_rate',
        'tax_amount',
        'deductions',
        'bonuses',
        'net_amount',
        'currency',
        'status',
        'calculated_at',
        'approved_at',
        'paid_at',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'notes',
        'calculated_by',
        'approved_by',
        'paid_by',
        'payment_method',
        'payment_reference',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_bookings' => 'integer',
        'total_hours' => 'decimal:2',
        'total_days' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'settlement_type' => SettlementType::class,
        'commission_type' => CommissionType::class,
        'status' => SettlementStatus::class,
        'payment_method' => PaymentMethod::class,
        'calculated_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($settlement) {
            if (empty($settlement->settlement_number)) {
                $settlement->settlement_number = $settlement->generateSettlementNumber();
            }
        });
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function settlementItems(): HasMany
    {
        return $this->hasMany(SettlementItem::class);
    }

    public function calculatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getResourceNameAttribute(): string
    {
        // For DataTable performance, return a simple identifier
        // The actual resource name can be loaded separately if needed
        return ucfirst($this->resource_type) . ' #' . $this->resource_id;
    }

    public function scopeByResourceType($query, $type)
    {
        return $query->where('resource_type', $type);
    }

    public function scopeBySettlementType($query, $type)
    {
        return $query->where('settlement_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_date', [$startDate, $endDate]);
    }

    public function scopeByMonth($query, $month, $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', SettlementStatus::PENDING);
    }

    public function scopeCalculated($query)
    {
        return $query->where('status', SettlementStatus::CALCULATED);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', SettlementStatus::APPROVED);
    }

    public function scopePaid($query)
    {
        return $query->where('status', SettlementStatus::PAID);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', SettlementStatus::REJECTED);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_amount, 2);
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->net_amount, 2);
    }

    public function getFormattedCommissionAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->commission_amount, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->tax_amount, 2);
    }

    public function getFormattedDeductionsAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->deductions, 2);
    }

    public function getFormattedBonusesAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->bonuses, 2);
    }

    public function getSettlementTypeLabelAttribute(): string
    {
        return $this->settlement_type->getLabel();
    }

    public function getCommissionTypeLabelAttribute(): string
    {
        return $this->commission_type->getLabel();
    }

    public function getMonthYearAttribute(): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return $months[$this->month] . ' ' . $this->year;
    }


    public function getResourceTypeLabelAttribute(): string
    {
        return match($this->resource_type) {
            'guide' => 'Guide',
            'representative' => 'Representative',
            'hotel' => 'Hotel',
            'vehicle' => 'Vehicle',
            'dahabia' => 'Dahabia',
            'restaurant' => 'Restaurant',
            'ticket' => 'Ticket',
            'extra' => 'Extra Service',
            default => 'Unknown'
        };
    }

    public function calculateSettlement(): void
    {
        $this->update([
            'status' => SettlementStatus::CALCULATED,
            'calculated_at' => now(),
        ]);
    }

    public function approve($userId): void
    {
        $this->update([
            'status' => SettlementStatus::APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function markAsPaid($userId): void
    {
        $this->update([
            'status' => SettlementStatus::PAID,
            'paid_at' => now(),
            'paid_by' => $userId,
        ]);
    }

    public function reject($reason, $userId): void
    {
        $this->update([
            'status' => SettlementStatus::REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'rejected_by' => $userId,
        ]);
    }

    public function generateSettlementNumber(): string
    {
        $prefix = 'STL';
        $date = now()->format('Ymd');
        
        // Get the last settlement number for today
        $lastSettlement = static::whereDate('created_at', now()->toDateString())
            ->whereNotNull('settlement_number')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastSettlement && $lastSettlement->settlement_number) {
            $lastRef = $lastSettlement->settlement_number;
            if (preg_match('/STL-' . $date . '-(\d+)/', $lastRef, $matches)) {
                $sequence = (int) $matches[1] + 1;
            } else {
                $sequence = 1;
            }
        } else {
            $sequence = 1;
        }
        
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequenceFormatted;
    }

    public function getFormattedSettlementNumberAttribute(): string
    {
        return $this->settlement_number ? 'Settlement Number: ' . $this->settlement_number : 'Not Specified';
    }
}
