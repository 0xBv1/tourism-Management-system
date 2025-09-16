<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierTripBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_trip_id',
        'user_id',
        'travel_date',
        'passengers_count',
        'selected_seats',
        'seats_booked',
        'total_price',
        'currency',
        'status',
        'booking_reference',
        'special_requests',
        'guest_name',
        'guest_email',
        'guest_phone',
    ];

    protected $casts = [
        'travel_date' => 'date',
        'passengers_count' => 'integer',
        'seats_booked' => 'integer',
        'total_price' => 'decimal:2',
        'selected_seats' => 'array',
    ];

    /**
     * Get the selected seats as an array.
     */
    public function getSelectedSeatsArrayAttribute()
    {
        if (is_string($this->selected_seats)) {
            return json_decode($this->selected_seats, true) ?: [];
        }
        return $this->selected_seats ?: [];
    }

    /**
     * Set the selected seats as JSON string.
     */
    public function setSelectedSeatsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['selected_seats'] = json_encode($value);
        } else {
            $this->attributes['selected_seats'] = $value;
        }
    }

    /**
     * Get the supplier trip that owns the booking.
     */
    public function supplierTrip(): BelongsTo
    {
        return $this->belongsTo(SupplierTrip::class);
    }

    /**
     * Get the user that made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSelectedSeatsCountAttribute(): int
    {
        return is_array($this->selected_seats) ? count($this->selected_seats) : 0;
    }

    public function getPriceBreakdownAttribute(): array
    {
        $trip = $this->supplierTrip;
        if (!$trip) {
            return [
                'seat_price' => 0,
                'total_price' => (float) $this->total_price,
                'seats_count' => $this->selected_seats_count ?: (int) $this->passengers_count,
                'selected_seats' => $this->selected_seats ?? [],
            ];
        }

        $seatCount = $this->selected_seats_count ?: (int) $this->passengers_count;
        $totalPrice = (float) $trip->seat_price * $seatCount;

        return [
            'seat_price' => (float) $trip->seat_price,
            'total_price' => (float) $this->total_price,
            'seats_count' => $seatCount,
            'selected_seats' => $this->selected_seats ?? [],
        ];
    }
}



