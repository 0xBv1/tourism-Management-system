<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierTrip extends Model
{
    use HasFactory, Enabled;

    protected $fillable = [
        'supplier_id',
        'trip_name',
        'trip_type',
        'departure_city',
        'arrival_city',
        'travel_date',
        'return_date',
        'departure_time',
        'arrival_time',
        'seat_price',
        'total_seats',
        'available_seats',
        'additional_notes',
        'amenities',
        'images',
        'featured_image',
        'enabled',
        'approved',
        'rejection_reason',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($trip) {
            if ($trip->total_seats > 0) {
                \App\Models\SupplierTripSeat::generateSeatsForTrip($trip->id, (int) $trip->total_seats);
            }
        });

        static::updating(function ($trip) {
            if ($trip->isDirty('total_seats')) {
                $oldSeats = (int) $trip->getOriginal('total_seats');
                $newSeats = (int) $trip->total_seats;

                if ($newSeats > $oldSeats) {
                    for ($i = $oldSeats + 1; $i <= $newSeats; $i++) {
                        // Check if seat already exists before creating
                        $existingSeat = \App\Models\SupplierTripSeat::where('supplier_trip_id', $trip->id)
                            ->where('seat_number', $i)
                            ->first();
                        
                        if (!$existingSeat) {
                            \App\Models\SupplierTripSeat::create([
                                'supplier_trip_id' => $trip->id,
                                'seat_number' => $i,
                                'is_available' => true,
                            ]);
                        }
                    }
                } elseif ($newSeats < $oldSeats) {
                    $trip->seats()
                        ->where('seat_number', '>', $newSeats)
                        ->where('is_available', true)
                        ->delete();
                }
            }
        });
    }

    protected $casts = [
        'travel_date' => 'date',
        'return_date' => 'date',
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'seat_price' => 'decimal:2',
        'total_seats' => 'integer',
        'available_seats' => 'integer',
        'amenities' => 'array',
        'images' => 'array',
        'enabled' => 'boolean',
        'approved' => 'boolean',
    ];

    protected $appends = [
        'formatted_seat_price',
        'formatted_total_price',
        'formatted_departure_time',
        'formatted_arrival_time',
        'status_label',
        'status_color',
        'trip_type_label',
        'is_available',
        'booked_seats',
        'occupancy_rate',
    ];

    // Trip type constants
    const TYPE_ONE_WAY = 'one_way';
    const TYPE_ROUND_TRIP = 'round_trip';
    const TYPE_SPECIAL_DISCOUNT = 'special_discount';

    /**
     * Get the supplier that owns the trip.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the bookings for this trip.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(SupplierTripBooking::class);
    }

    /**
     * Seats for this supplier trip.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(SupplierTripSeat::class, 'supplier_trip_id');
    }

    /**
     * Get the formatted seat price attribute.
     */
    public function getFormattedSeatPriceAttribute(): string
    {
        return number_format($this->seat_price, 2) . ' EGP';
    }

    /**
     * Get the formatted total price attribute.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->seat_price * $this->total_seats, 2) . ' EGP';
    }

    /**
     * Get the formatted departure time attribute.
     */
    public function getFormattedDepartureTimeAttribute(): string
    {
        return $this->departure_time ? $this->departure_time->format('H:i') : '';
    }

    /**
     * Get the formatted arrival time attribute.
     */
    public function getFormattedArrivaltimeAttribute(): string
    {
        return $this->arrival_time ? $this->arrival_time->format('H:i') : '';
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->approved) {
            return 'Pending Approval';
        }
        return $this->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->approved) {
            return 'warning';
        }
        return $this->enabled ? 'success' : 'secondary';
    }

    /**
     * Get the trip type label attribute.
     */
    public function getTripTypeLabelAttribute(): string
    {
        return self::getTripTypes()[$this->trip_type] ?? $this->trip_type ?? 'Not Set';
    }

    /**
     * Get the is available attribute.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->available_seats > 0 && $this->enabled && $this->approved;
    }

    /**
     * Get the booked seats attribute.
     */
    public function getBookedSeatsAttribute(): int
    {
        return $this->total_seats - $this->available_seats;
    }

    /**
     * Get the occupancy rate attribute.
     */
    public function getOccupancyRateAttribute(): float
    {
        if (empty($this->total_seats) || $this->total_seats <= 0) {
            return 0;
        }
        return round((($this->total_seats - $this->available_seats) / $this->total_seats) * 100, 2);
    }

    /**
     * Business helpers similar to dashboard Trip model.
     */
    public function hasAvailableSeats(int $passengers): bool
    {
        return (int) $this->available_seats >= $passengers;
    }

    public function hasAvailableSeatsForNumbers(array $seatNumbers): bool
    {
        return $this->seats()
            ->whereIn('seat_number', $seatNumbers)
            ->where('is_available', true)
            ->count() === count($seatNumbers);
    }

    public function getAvailableSeats(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->seats()->available()->orderBy('seat_number')->get();
    }

    public function getAllSeats(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->seats()->orderBy('seat_number')->get();
    }

    public function getBookedSeatsCollection(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->seats()->booked()->orderBy('seat_number')->get();
    }

    public function calculateTotalPrice(int $passengers): float
    {
        return (float) $this->seat_price * $passengers;
    }

    public function calculateTotalPriceForSeats(array $seatNumbers): float
    {
        return (float) $this->seat_price * count($seatNumbers);
    }

    public function calculatePriceBreakdown(int $passengers): array
    {
        $passengerPrice = (float) $this->seat_price * $passengers;

        return [
            'passenger_price' => $passengerPrice,
            'total_price' => $passengerPrice,
            'passengers' => $passengers,
            'seat_price' => (float) $this->seat_price,
        ];
    }

    public function calculatePriceBreakdownForSeats(array $seatNumbers): array
    {
        $seatPrice = (float) $this->seat_price * count($seatNumbers);

        return [
            'seat_price' => (float) $this->seat_price,
            'total_price' => $seatPrice,
            'seats_count' => count($seatNumbers),
            'selected_seats' => $seatNumbers,
        ];
    }

    /**
     * Get trip types.
     */
    public static function getTripTypes(): array
    {
        return [
            self::TYPE_ONE_WAY => 'One Way',
            self::TYPE_ROUND_TRIP => 'Round Trip',
            self::TYPE_SPECIAL_DISCOUNT => 'Special Discount'
        ];
    }

    /**
     * Scope to filter by approved trips.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope to filter by trip type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('trip_type', $type);
    }

    /**
     * Scope to filter by cities.
     */
    public function scopeByCities($query, $from, $to)
    {
        return $query->where('departure_city', $from)->where('arrival_city', $to);
    }

    /**
     * Scope to filter by date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('travel_date', $date);
    }

    /**
     * Scope to filter available trips.
     */
    public function scopeAvailable($query)
    {
        return $query->where('enabled', true)
                    ->where('approved', true)
                    ->where('available_seats', '>', 0);
    }
}



