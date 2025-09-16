<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierTripSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_trip_id',
        'seat_number',
        'is_available',
        'booking_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    protected $appends = [
        'status_label',
        'status_color'
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(SupplierTrip::class, 'supplier_trip_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(SupplierTripBooking::class, 'booking_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_available ? 'متاح' : 'محجوز';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_available ? 'success' : 'danger';
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeBooked($query)
    {
        return $query->where('is_available', false);
    }

    public function scopeByTrip($query, $tripId)
    {
        return $query->where('supplier_trip_id', $tripId);
    }

    public function scopeBySeatNumbers($query, array $seatNumbers)
    {
        return $query->whereIn('seat_number', $seatNumbers);
    }
    public static function generateSeatsForTrip(int $tripId, int $totalSeats): void
    {
        // Check if seats already exist for this trip
        $existingSeats = self::where('supplier_trip_id', $tripId)->count();
        if ($existingSeats > 0) {
            return; // Seats already exist, don't create duplicates
        }

        $seats = [];
        for ($i = 1; $i <= $totalSeats; $i++) {
            $seats[] = [
                'supplier_trip_id' => $tripId,
                'seat_number' => $i,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        self::insert($seats);
    }

    public static function getAvailableSeatsForTrip(int $tripId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('supplier_trip_id', $tripId)->available()->orderBy('seat_number')->get();
    }

    public static function bookSeats(int $tripId, array $seatNumbers, int $bookingId): bool
    {
        $seats = self::where('supplier_trip_id', $tripId)
            ->whereIn('seat_number', $seatNumbers)
            ->available()
            ->get();

        if ($seats->count() !== count($seatNumbers)) {
            return false;
        }

        return $seats->every(function ($seat) use ($bookingId) {
            return $seat->update([
                'is_available' => false,
                'booking_id' => $bookingId
            ]);
        });
    }

    public static function releaseSeats(int $bookingId): bool
    {
        return self::where('booking_id', $bookingId)->update([
            'is_available' => true,
            'booking_id' => null
        ]);
    }
}




