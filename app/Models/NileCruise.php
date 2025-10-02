<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\ActivatedAndEnabled;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class NileCruise extends Model
{
    use ActivatedAndEnabled;

    protected $fillable = [
        'name',
        'description',
        'city_id',
        'vessel_type',
        'capacity',
        'price_per_person',
        'price_per_cabin',
        'currency',
        'departure_location',
        'arrival_location',
        'itinerary',
        'meal_plan',
        'amenities',
        'images',
        'status',
        'active',
        'enabled',
        'check_in_time',
        'check_out_time',
        'duration_nights',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'price_per_person' => 'decimal:2',
        'price_per_cabin' => 'decimal:2',
        'capacity' => 'integer',
        'duration_nights' => 'integer',
        'itinerary' => 'array',
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
            ->where('resource_type', 'nile_cruise');
    }

    /**
     * Scope a query to only include active resources.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope a query to only include enabled resources.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope a query to filter by city.
     */
    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    /**
     * Scope a query to filter by vessel type.
     */
    public function scopeByVesselType($query, $vesselType)
    {
        return $query->where('vessel_type', $vesselType);
    }

    /**
     * Scope a query to filter by capacity range.
     */
    public function scopeByCapacity($query, $minCapacity = null, $maxCapacity = null)
    {
        if ($minCapacity) {
            $query->where('capacity', '>=', $minCapacity);
        }
        if ($maxCapacity) {
            $query->where('capacity', '<=', $maxCapacity);
        }
        return $query;
    }

    /**
     * Scope a query to filter by meal plan.
     */
    public function scopeByMealPlan($query, $mealPlan)
    {
        return $query->where('meal_plan', $mealPlan);
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

    /**
     * Get formatted price display
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price_per_person && $this->price_per_cabin) {
            return "Person: {$this->currency} " . number_format($this->price_per_person, 2) . 
                   " | Cabin: {$this->currency} " . number_format($this->price_per_cabin, 2);
        } elseif ($this->price_per_person) {
            return "Person: {$this->currency} " . number_format($this->price_per_person, 2);
        } elseif ($this->price_per_cabin) {
            return "Cabin: {$this->currency} " . number_format($this->price_per_cabin, 2);
        }
        return 'No pricing available';
    }
}
