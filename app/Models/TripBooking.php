<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripBooking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'client_id',
        'passenger_name',
        'passenger_email',
        'passenger_phone',
        'number_of_passengers',
        'total_price',
        'notes',
        'status', // pending, confirmed, cancelled, completed
        'booking_reference',
        'children_count',
        'adults_count'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'number_of_passengers' => 'integer',
        'children_count' => 'integer',
        'adults_count' => 'integer',
    ];

    protected $appends = [
        'status_label',
        'formatted_total_price',
        'price_breakdown',
        'formatted_price_breakdown',
        'status_color'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_CONFIRMED => 'confirmed',
            self::STATUS_CANCELLED => 'cancelled',
            self::STATUS_COMPLETED => 'completed'
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total_price, 2);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTrip($query, $tripId)
    {
        return $query->where('trip_id', $tripId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public static function generateBookingReference(): string
    {
        return 'TB-' . strtoupper(uniqid());
    }

    public function calculateTotalPrice(): float
    {
        $trip = $this->trip;
        if (!$trip) {
            return 0;
        }

        // Calculate price based on adults and children
        $adultPrice = $trip->seat_price * $this->adults_count;
        $childPrice = $trip->seat_price * 0.5 * $this->children_count; // 50% discount for children

        return $adultPrice + $childPrice;
    }

    public function getPriceBreakdownAttribute(): array
    {
        $trip = $this->trip;
        if (!$trip) {
            return [
                'adult_price' => 0,
                'child_price' => 0,
                'total_price' => 0,
                'adults' => $this->adults_count,
                'children' => $this->children_count,
                'seat_price' => 0,
                'child_discount_rate' => 0.5
            ];
        }

        $adultPrice = $trip->seat_price * $this->adults_count;
        $childPrice = $trip->seat_price * 0.5 * $this->children_count;

        return [
            'adult_price' => $adultPrice,
            'child_price' => $childPrice,
            'total_price' => $this->total_price,
            'adults' => $this->adults_count,
            'children' => $this->children_count,
            'seat_price' => $trip->seat_price,
            'child_discount_rate' => 0.5
        ];
    }

    public function getFormattedPriceBreakdownAttribute(): string
    {
        $breakdown = $this->price_breakdown;
        
        $parts = [];
        if ($breakdown['adults'] > 0) {
            $parts[] = $breakdown['adults'] . ' Adults: ' . number_format($breakdown['adult_price'], 2) . ' EGP';
        }
        if ($breakdown['children'] > 0) {
            $parts[] = $breakdown['children'] . ' Children: ' . number_format($breakdown['child_price'], 2) . ' EGP';
        }
        
        return implode(' + ', $parts);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'secondary'
        };
    }
}
