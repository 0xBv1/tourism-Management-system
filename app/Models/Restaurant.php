<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\Models\ActivatedAndEnabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
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
        'cuisine_type',
        'price_range',
        'currency',
        'cuisines',
        'status',
        'active',
        'enabled',
        'capacity',
        'reservation_required',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'capacity' => 'integer',
        'reservation_required' => 'boolean',
        'cuisines' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'restaurant');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByCuisineType($query, $cuisineType)
    {
        return $query->where('cuisine_type', $cuisineType);
    }

    public function scopeByCuisines($query, $cuisines)
    {
        return $query->whereJsonContains('cuisines', $cuisines);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        // Price range is now categorical, so we'll use price_range field instead
        return $query->whereNotNull('price_range');
    }

    public function scopeByPriceRangeCategory($query, $priceRange)
    {
        return $query->where('price_range', $priceRange);
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    public function scopeReservationRequired($query, $required = true)
    {
        return $query->where('reservation_required', $required);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status ? $this->status->getColor() : ResourceStatus::AVAILABLE->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? $this->status->getLabel() : ResourceStatus::AVAILABLE->getLabel();
    }

    public function isAvailable(): bool
    {
        return $this->status === ResourceStatus::AVAILABLE;
    }
}
