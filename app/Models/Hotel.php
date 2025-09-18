<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\Models\ActivatedAndEnabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes, ActivatedAndEnabled;

    protected $fillable = [
        'name',
        'description',
        'address',
        'city_id',
        'phone',
        'email',
        'website',
        'star_rating',
        'total_rooms',
        'available_rooms',
        'price_per_night',
        'currency',
        'amenities',
        'images',
        'status',
        'active',
        'enabled',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'star_rating' => 'integer',
        'total_rooms' => 'integer',
        'available_rooms' => 'integer',
        'price_per_night' => 'decimal:2',
        'amenities' => 'array',
        'images' => 'array',
        'check_in_time' => 'datetime:H:i',
        'check_out_time' => 'datetime:H:i',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'hotel');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE)
            ->where('available_rooms', '>', 0);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByStarRating($query, $rating)
    {
        return $query->where('star_rating', $rating);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_night', [$minPrice, $maxPrice]);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    public function getUtilizationPercentageAttribute(): float
    {
        if ($this->total_rooms == 0) {
            return 0;
        }
        
        $occupiedRooms = $this->total_rooms - $this->available_rooms;
        return round(($occupiedRooms / $this->total_rooms) * 100, 2);
    }

    public function isAvailable(): bool
    {
        return $this->status === ResourceStatus::AVAILABLE && $this->available_rooms > 0;
    }

    public function updateAvailableRooms(int $change): void
    {
        $newAvailable = max(0, $this->available_rooms + $change);
        $this->update(['available_rooms' => $newAvailable]);
    }
}




