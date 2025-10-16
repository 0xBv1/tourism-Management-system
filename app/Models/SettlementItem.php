<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettlementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_id',
        'resource_booking_id',
        'booking_file_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'duration_days',
        'unit_price',
        'total_price',
        'currency',
        'client_name',
        'tour_name',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_hours' => 'decimal:2',
        'duration_days' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class);
    }

    public function resourceBooking(): BelongsTo
    {
        return $this->belongsTo(ResourceBooking::class);
    }

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->total_price, 2);
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->unit_price, 2);
    }

    public function getDurationTextAttribute(): string
    {
        if ($this->duration_days > 0) {
            return $this->duration_days . ' يوم';
        } elseif ($this->duration_hours > 0) {
            return $this->duration_hours . ' ساعة';
        }
        return 'غير محدد';
    }
}

