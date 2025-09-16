<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class SupplierHotel extends Model
{
    use HasFactory, Enabled, Translatable, HasSeo, HasAutoSlug;

    public array $translatedAttributes = [
        'name',
        'description',
        'city'
    ];

    protected $fillable = [
        'supplier_id',
        'stars',
        'enabled',
        'featured_image',
        'banner',
        'gallery',
        'address',
        'map_iframe',
        'slug',
        'phone_contact',
        'whatsapp_contact',
        'approved',
        'rejection_reason',
    ];

    protected $casts = [
        'stars' => 'integer',
        'price_per_night' => 'decimal:2',
        'gallery' => 'array',
        'enabled' => 'boolean',
        'approved' => 'boolean',
    ];

    protected $hidden = [
        'translations'
    ];

    protected $appends = [
        'formatted_price',
        'status_label',
        'status_color',
    ];

    /**
     * Get the supplier that owns the hotel.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the bookings for this hotel.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(SupplierHotelBooking::class);
    }

    /**
     * Get the amenities for this hotel.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'supplier_hotel_amenities');
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price_per_night, 2) . ' ' . $this->currency;
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
     * Scope to filter by approved hotels.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope to filter by city.
     */
    public function scopeByCity($query, $city)
    {
        return $query->whereHas('translations', function ($q) use ($city) {
            $q->where('city', 'LIKE', '%' . $city . '%');
        });
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price_per_night', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter by stars.
     */
    public function scopeByStars($query, $stars)
    {
        return $query->where('stars', $stars);
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