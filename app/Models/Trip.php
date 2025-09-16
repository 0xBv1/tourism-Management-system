<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'trip_type',
        'departure_city_id',
        'arrival_city_id',
        'travel_date',
        'return_date',
        'departure_time',
        'arrival_time',
        'seat_price',
        'total_seats',
        'available_seats',
        'additional_notes',
        'amenities',
        'enabled',
        'trip_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'amenities' => 'array',
        'enabled' => 'boolean',
    ];

    /**
     * Get the departure city for this trip.
     */
    public function departureCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'departure_city_id');
    }

    /**
     * Get the arrival city for this trip.
     */
    public function arrivalCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'arrival_city_id');
    }

    /**
     * Get the trip seats for this trip.
     */
    public function tripSeats(): HasMany
    {
        return $this->hasMany(TripSeat::class);
    }

    /**
     * Get the trip bookings for this trip.
     */
    public function tripBookings(): HasMany
    {
        return $this->hasMany(TripBooking::class);
    }

    /**
     * Scope to get only available trips.
     */
    public function scopeAvailable($query)
    {
        return $query->where('enabled', true)
                    ->where('available_seats', '>', 0)
                    ->where('travel_date', '>=', now()->toDateString());
    }

    /**
     * Get the trip type label.
     */
    public function getTripTypeLabelAttribute(): string
    {
        return match($this->trip_type) {
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
            'special_discount' => 'Special Discount',
            default => ucfirst(str_replace('_', ' ', $this->trip_type))
        };
    }

    /**
     * Get the departure city name.
     */
    public function getDepartureCityNameAttribute(): string
    {
        return $this->departureCity->name ?? 'N/A';
    }

    /**
     * Get the arrival city name.
     */
    public function getArrivalCityNameAttribute(): string
    {
        return $this->arrivalCity->name ?? 'N/A';
    }

    /**
     * Get formatted departure time.
     */
    public function getFormattedDepartureTimeAttribute(): string
    {
        return $this->departure_time ? $this->departure_time->format('H:i') : 'N/A';
    }

    /**
     * Get formatted arrival time.
     */
    public function getFormattedArrivalTimeAttribute(): string
    {
        return $this->arrival_time ? $this->arrival_time->format('H:i') : 'N/A';
    }

    /**
     * Calculate occupancy rate.
     */
    public function getOccupancyRateAttribute(): float
    {
        if ($this->total_seats <= 0) {
            return 0.0;
        }
        
        $bookedSeats = $this->total_seats - $this->available_seats;
        return round(($bookedSeats / $this->total_seats) * 100, 1);
    }

    /**
     * Get occupancy status.
     */
    public function getOccupancyStatusAttribute(): string
    {
        $rate = $this->occupancy_rate;
        
        if ($rate >= 90) {
            return 'full';
        } elseif ($rate >= 70) {
            return 'limited';
        } else {
            return 'available';
        }
    }

    /**
     * Calculate total price for given number of passengers.
     */
    public function calculateTotalPrice(int $passengers): float
    {
        return $this->seat_price * $passengers;
    }

    /**
     * Calculate total price for specific seats.
     */
    public function calculateTotalPriceForSeats(array $seats): float
    {
        // For now, assume all seats have the same price
        // In a real implementation, you might have different seat prices
        return $this->seat_price * count($seats);
    }
}
