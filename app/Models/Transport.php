<?php

namespace App\Models;

use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use App\Traits\Models\Enabled;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property string $description
 */
class Transport extends Model
{
    use Translatable, HasSeo, HasAutoSlug, Enabled;

    public array $translatedAttributes = [
        'name',
        'description'
    ];

    protected $fillable = [
        'transport_type',
        'vehicle_type',
        'seating_capacity',
        'origin_location',
        'destination_location',
        'intermediate_stops',
        'estimated_travel_time',
        'distance',
        'route_type',
        'price',
        'currency',
        'vehicle_registration',
        'images',
        'featured_image',
        'enabled',
        'slug',
        'phone_contact',
        'whatsapp_contact',
        'email_contact',
        'contact_notes',
        'departure_time',
        'arrival_time',
        'departure_location',
        'arrival_location',
        'schedule_notes',
        'price_per_hour',
        'price_per_day',
        'price_per_km',
        'discount_percentage',
        'discount_conditions',
        'pricing_notes',
        'vehicle_images',
        'route_map',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'images' => 'array',
        'vehicle_images' => 'array',
        'intermediate_stops' => 'array',
        'distance' => 'decimal:2',
        'price' => 'decimal:2',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'estimated_travel_time' => 'integer',
        'seating_capacity' => 'integer',
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
    ];

    protected $hidden = [
        'translations'
    ];

    protected $appends = [
        'formatted_price',
        'formatted_travel_time',
        'status_label',
        'status_color',
        'route_display',
        'formatted_distance',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(TransportBooking::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'transport_amenities');
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted travel time attribute.
     */
    public function getFormattedTravelTimeAttribute(): string
    {
        if (!$this->estimated_travel_time) {
            return 'N/A';
        }
        
        $hours = floor($this->estimated_travel_time / 60);
        $minutes = $this->estimated_travel_time % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }

    /**
     * Get the status label attribute.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->enabled ? 'Active' : 'Inactive';
    }

    /**
     * Get the status color attribute.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->enabled ? 'success' : 'danger';
    }

    /**
     * Get the route display attribute.
     */
    public function getRouteDisplayAttribute(): string
    {
        return "{$this->origin_location} → {$this->destination_location}";
    }

    /**
     * Get the distance in a formatted way.
     */
    public function getFormattedDistanceAttribute(): ?string
    {
        if (!$this->distance) {
            return null;
        }
        
        if ($this->distance >= 1) {
            return number_format($this->distance, 1) . ' km';
        }
        
        return number_format($this->distance * 1000, 0) . ' m';
    }

    /**
     * Scope to filter by transport type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('transport_type', $type);
    }

    /**
     * Scope to filter by vehicle type.
     */
    public function scopeByVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    /**
     * Scope to filter by route type.
     */
    public function scopeByRouteType($query, $routeType)
    {
        return $query->where('route_type', $routeType);
    }

    /**
     * Scope to search in transport details.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('origin_location', 'like', "%{$search}%")
              ->orWhere('destination_location', 'like', "%{$search}%")
              ->orWhere('route_type', 'like', "%{$search}%")
              ->orWhere('vehicle_type', 'like', "%{$search}%")
              ->orWhere('intermediate_stops', 'like', "%{$search}%");
        });
    }

    /**
     * Get the total revenue from bookings.
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()->where('status', 'completed')->sum('total_price');
    }

    /**
     * Get the average rating from bookings.
     */
    public function getAverageRatingAttribute()
    {
        $ratings = $this->bookings()->whereNotNull('rating')->pluck('rating');
        return $ratings->count() > 0 ? round($ratings->avg(), 1) : null;
    }

    /**
     * Check if transport is available for booking.
     */
    public function isAvailableForBooking(): bool
    {
        return $this->enabled;
    }

    /**
     * Generate a unique slug from the given text.
     * This is a static method for external use.
     */
    public static function generateSlug(string $text, $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($text);
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug already exists and make it unique
        while (static::where('slug', $slug)->where('id', '!=', $excludeId ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
