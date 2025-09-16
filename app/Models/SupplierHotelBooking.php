<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierHotelBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_hotel_id',
        'client_id',
        'supplier_id',
        'check_in_date',
        'check_out_date',
        'adults',
        'children',
        'infants',
        'total_price',
        'commission_amount',
        'supplier_amount',
        'currency',
        'status',
        'special_requests',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_price' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'supplier_amount' => 'decimal:2',
        'adults' => 'integer',
        'children' => 'integer',
        'infants' => 'integer',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_total_price',
        'formatted_commission_amount',
        'formatted_supplier_amount',
        'status_label',
        'status_color',
        'total_guests',
    ];

    /**
     * Get the hotel that owns the booking.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(SupplierHotel::class, 'supplier_hotel_id');
    }

    /**
     * Get the client that owns the booking.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the supplier that owns the booking.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted commission amount attribute.
     */
    public function getFormattedCommissionAmountAttribute(): string
    {
        return number_format($this->commission_amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted supplier amount attribute.
     */
    public function getFormattedSupplierAmountAttribute(): string
    {
        return number_format($this->supplier_amount, 2) . ' ' . $this->currency;
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary',
        };
    }

    /**
     * Get the total guests attribute.
     */
    public function getTotalGuestsAttribute(): int
    {
        return $this->adults + $this->children + $this->infants;
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('check_in_date', [$startDate, $endDate]);
    }
}
