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

class Guide extends Model
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
     * Scope a query to only include active guides.
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
        'email',
        'phone',
        'nationality',
        'languages',
        'specializations',
        'experience_years',
        'city_id',
        'price_per_hour',
        'price_per_day',
        'currency',
        'bio',
        'certifications',
        'profile_image',
        'status',
        'active',
        'enabled',
        'rating',
        'total_ratings',
        'availability_schedule',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    protected $casts = [
        'status' => ResourceStatus::class,
        'active' => 'boolean',
        'enabled' => 'boolean',
        'languages' => 'array',
        'specializations' => 'array',
        'experience_years' => 'integer',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'certifications' => 'array',
        'rating' => 'decimal:1',
        'total_ratings' => 'integer',
        'availability_schedule' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'guide');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', ResourceStatus::AVAILABLE);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->whereJsonContains('languages', $language);
    }

    public function scopeBySpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeByExperience($query, $minYears)
    {
        return $query->where('experience_years', '>=', $minYears);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
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

    public function getAverageRatingAttribute(): float
    {
        return $this->total_ratings > 0 ? round($this->rating / $this->total_ratings, 1) : 0;
    }

    public function updateRating(float $newRating): void
    {
        $totalRating = ($this->rating * $this->total_ratings) + $newRating;
        $this->total_ratings += 1;
        $this->rating = $totalRating / $this->total_ratings;
        $this->save();
    }

    public function isAvailableOnDate(\DateTime $date): bool
    {
        if (!$this->availability_schedule) {
            return true;
        }

        $dayOfWeek = strtolower($date->format('l'));
        return isset($this->availability_schedule[$dayOfWeek]) && $this->availability_schedule[$dayOfWeek];
    }
}




