<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\Translations\SupplierTransportTranslation;

class SupplierTransport extends Model implements TranslatableContract
{
    use HasFactory, Enabled, Translatable, HasAutoSlug, HasSeo;

    public array $translatedAttributes = ['name', 'description'];
    
    protected string $translationModel = SupplierTransportTranslation::class;

    protected $fillable = [
        'supplier_id',
        'origin_city_id',
        'destination_city_id',
        'origin_location',
        'destination_location',
        'intermediate_stops',
        'estimated_travel_time',
        'distance',
        'route_type',
        'price',
        'currency',
        'vehicle_type',
        'seating_capacity',
        'vehicle_registration',
        'amenities',
        'images',
        'featured_image',
        'enabled',
        'approved',
        'rejection_reason',
        // Schedule fields
        'departure_time',
        'arrival_time',
        'departure_location',
        'arrival_location',
        'schedule_notes',
        // Pricing fields
        'price_per_hour',
        'price_per_day',
        'price_per_km',
        'discount_percentage',
        'discount_conditions',
        'pricing_notes',
        // Contact fields
        'contact_person',
        'phone_contact',
        'whatsapp_contact',
        'email_contact',
        'contact_notes',
        // Media fields
        'vehicle_images',
        'route_map',
        'slug',
    ];

    protected $casts = [
        'intermediate_stops' => 'array',
        'distance' => 'decimal:2',
        'price' => 'decimal:2',
        'estimated_travel_time' => 'integer',
        'seating_capacity' => 'integer',
        'amenities' => 'array',
        'images' => 'array',
        'enabled' => 'boolean',
        'approved' => 'boolean',
        // Schedule fields
        'departure_time' => 'datetime:H:i',
        'arrival_time' => 'datetime:H:i',
        // Pricing fields
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        // Media fields
        'vehicle_images' => 'array',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_travel_time',
        'status_label',
        'status_color',
    ];

    /**
     * Get the supplier that owns the transport.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the origin city.
     */
    public function originCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'origin_city_id');
    }

    /**
     * Get the destination city.
     */
    public function destinationCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    /**
     * Get the bookings for this transport.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(SupplierTransportBooking::class);
    }

    /**
     * Get the amenities for this transport.
     */
    public function amenities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Amenity::class, 'supplier_transport_amenities');
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
     * Scope to filter by approved transports.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope to filter by route type.
     */
    public function scopeByRouteType($query, $type)
    {
        return $query->where('route_type', $type);
    }

    /**
     * Scope to filter by locations.
     */
    public function scopeByLocations($query, $origin, $destination)
    {
        return $query->where('origin_location', 'like', "%{$origin}%")
                    ->where('destination_location', 'like', "%{$destination}%");
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter by vehicle type.
     */
    public function scopeByVehicleType($query, $vehicleType)
    {
        return $query->where('vehicle_type', $vehicleType);
    }

    /**
     * Scope to filter by enabled transports.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to filter by pending approval.
     */
    public function scopePending($query)
    {
        return $query->where('approved', false);
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
              ->orWhere('intermediate_stops', 'like', "%{$search}%")
              ->orWhere('amenities', 'like', "%{$search}%");
        });
    }

    /**
     * Get the total revenue from bookings.
     */
    public function getTotalRevenueAttribute()
    {
        return $this->bookings()->where('status', 'completed')->sum('total_amount');
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
        return $this->enabled && $this->approved;
    }

    /**
     * Get the route display name.
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
     * Get the amenities as an array.
     */
    public function getAmenitiesArrayAttribute(): array
    {
        if (!$this->amenities) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $this->amenities)));
    }

    /**
     * Get the intermediate stops as an array.
     */
    public function getIntermediateStopsArrayAttribute(): array
    {
        if (!$this->intermediate_stops) {
            return [];
        }
        
        return array_filter(array_map('trim', explode(',', $this->intermediate_stops)));
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



