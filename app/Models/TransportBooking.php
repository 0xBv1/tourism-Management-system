<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransportBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'transport_id',
        'client_id',
        'travel_date',
        'pickup_time',
        'pickup_location',
        'dropoff_location',
        'passengers',
        'price_per_passenger',
        'total_price',
        'currency',
        'status',
        'special_requests',
        'cancellation_reason',
        'confirmed_at',
        'cancelled_at',
        'rating',
        'review',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'pickup_time' => 'datetime',
        'price_per_passenger' => 'decimal:2',
        'total_price' => 'decimal:2',
        'passengers' => 'integer',
        'rating' => 'integer',
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_total_price',
        'status_label',
        'status_color',
        'formatted_pickup_time',
    ];

    /**
     * Get the transport that owns the booking.
     */
    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }

    /**
     * Get the client that owns the booking.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the formatted pickup time attribute.
     */
    public function getFormattedPickupTimeAttribute(): string
    {
        return $this->pickup_time ? $this->pickup_time->format('H:i') : 'N/A';
    }
}
