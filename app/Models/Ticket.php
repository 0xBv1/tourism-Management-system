<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use App\Traits\Models\ActivatedAndEnabled;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes, ActivatedAndEnabled;

    protected $fillable = [
        'name',
        'description',
        'city_id',
        'price_per_person',
        'currency',
        'duration_hours',
        'images',
        'status',
        'active',
        'enabled',
        'min_age',
        'max_age',
        'max_participants',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'price_per_person' => 'decimal:2',
        'duration_hours' => 'decimal:2',
        'max_participants' => 'integer',
        'min_age' => 'integer',
        'max_age' => 'integer',
        'images' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'ticket');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_person', [$minPrice, $maxPrice]);
    }

    public function scopeByDuration($query, $maxDuration)
    {
        return $query->where('duration_hours', '<=', $maxDuration);
    }

    public function scopeByAgeRange($query, $minAge, $maxAge)
    {
        return $query->where(function($q) use ($minAge, $maxAge) {
            $q->whereNull('min_age')->orWhere('min_age', '<=', $minAge);
            $q->whereNull('max_age')->orWhere('max_age', '>=', $maxAge);
        });
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
