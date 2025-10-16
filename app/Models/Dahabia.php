<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\Models\ActivatedAndEnabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dahabia extends Model
{
    use HasFactory, SoftDeletes, ActivatedAndEnabled;

    protected $fillable = [
        'name',
        'description',
        'city_id',
        'vessel_length',
        'capacity',
        'price_per_person',
        'price_per_charter',
        'currency',
        'departure_location',
        'arrival_location',
        'route_description',
        'sailing_schedule',
        'meal_plan',
        'amenities',
        'images',
        'status',
        'active',
        'enabled',
        'crew_count',
        'duration_nights',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'capacity' => 'integer',
        'price_per_person' => 'decimal:2',
        'price_per_charter' => 'decimal:2',
        'crew_count' => 'integer',
        'duration_nights' => 'integer',
        'vessel_length' => 'decimal:2',
        'sailing_schedule' => 'array',
        'amenities' => 'array',
        'images' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'dahabia');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'resource_id')
            ->where('resource_type', 'dahabia');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->where('enabled', true);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    public function scopeByDuration($query, $nights)
    {
        return $query->where('duration_nights', $nights);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice, $priceType = 'person')
    {
        $priceColumn = $priceType === 'charter' ? 'price_per_charter' : 'price_per_person';
        return $query->whereBetween($priceColumn, [$minPrice, $maxPrice]);
    }

    public function scopeByVesselLength($query, $minLength)
    {
        return $query->where('vessel_length', '>=', $minLength);
    }

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
}
