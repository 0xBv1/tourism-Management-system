<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\Models\Activated;
use App\Traits\Models\Enabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, Activated, Enabled {
        Activated::booted insteadof Enabled;
        Activated::scopeActive insteadof Enabled;
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        // Call both trait booted methods
        static::addGlobalScope(new \App\Scopes\Activated);
        static::addGlobalScope(new \App\Scopes\Enabled);
    }

    /**
     * Scope a query to only include active vehicles.
     * This method handles both 'active' and 'enabled' fields.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $active
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $active = true)
    {
        return $query->where('active', $active)->where('enabled', $active);
    }

    protected $fillable = [
        'name',
        'type',
        'brand',
        'model',
        'year',
        'license_plate',
        'capacity',
        'description',
        'city_id',
        'driver_name',
        'driver_phone',
        'driver_license',
        'price_per_hour',
        'price_per_day',
        'currency',
        'fuel_type',
        'transmission',
        'features',
        'images',
        'status',
        'active',
        'enabled',
        'insurance_expiry',
        'registration_expiry',
        'last_maintenance',
        'next_maintenance',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'year' => 'integer',
        'capacity' => 'integer',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'features' => 'array',
        'images' => 'array',
        'insurance_expiry' => 'date',
        'registration_expiry' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'vehicle');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'resource_id')
            ->where('resource_type', 'vehicle');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCapacity($query, $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice, $priceType = 'day')
    {
        $priceColumn = $priceType === 'hour' ? 'price_per_hour' : 'price_per_day';
        return $query->whereBetween($priceColumn, [$minPrice, $maxPrice]);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    public function isAvailable(): bool
    {
        return $this->status === ResourceStatus::AVAILABLE;
    }

    public function needsMaintenance(): bool
    {
        return $this->next_maintenance && $this->next_maintenance <= now()->addDays(7);
    }

    public function isInsuranceExpired(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry < now();
    }

    public function isRegistrationExpired(): bool
    {
        return $this->registration_expiry && $this->registration_expiry < now();
    }
}




