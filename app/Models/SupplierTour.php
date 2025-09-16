<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasSeo;
use App\Traits\Models\HasAutoSlug;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierTour extends Model
{
    use HasFactory, Enabled, Translatable, HasSeo, HasAutoSlug;

    public array $translatedAttributes = [
        'title',
        'description',
        'highlights',
        'included',
        'excluded',
        'itinerary',
        'duration',
        'type',
        'run',
        'pickup_time',
        'overview'
    ];

    protected $fillable = [
        'supplier_id',
        'title',
        'slug',
        'display_order',
        'enabled',
        'featured',
        'code',
        'duration',
        'duration_in_days',
        'type',
        'pickup_location',
        'dropoff_location',
        'adult_price',
        'child_price',
        'infant_price',
        'currency',
        'max_group_size',
        'itinerary',
        'images',
        'featured_image',
        'gallery',
        'pricing_groups',
        'approved',
        'rejection_reason',
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'infant_price' => 'decimal:2',
        'max_group_size' => 'integer',
        'display_order' => 'integer',
        'duration_in_days' => 'integer',
        'images' => 'array',
        'gallery' => 'array',
        'pricing_groups' => 'array',
        'enabled' => 'boolean',
        'featured' => 'boolean',
        'approved' => 'boolean',
    ];

    protected $hidden = [
        'translations'
    ];

    protected $appends = [
        'formatted_adult_price',
        'formatted_child_price',
        'formatted_infant_price',
        'status_label',
        'status_color',
    ];

    /**
     * Get the supplier that owns the tour.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the bookings for this tour.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(SupplierTourBooking::class);
    }

    /**
     * Get the categories for this tour.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'supplier_tour_categories');
    }

    /**
     * Get the destinations for this tour.
     */
    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'supplier_tour_destinations');
    }

    /**
     * Get the options for this tour.
     */
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(TourOption::class, 'supplier_tour_options');
    }

    /**
     * Get the durations for this tour.
     */
    public function durations(): BelongsToMany
    {
        return $this->belongsToMany(Duration::class, 'supplier_tour_durations');
    }

    /**
     * Get the tour days for this tour.
     */
    public function tourDays(): HasMany
    {
        return $this->hasMany(SupplierTourDay::class);
    }

    /**
     * Get the tour days for this tour (alias for tourDays).
     */
    public function days(): HasMany
    {
        return $this->tourDays();
    }

    /**
     * Get the formatted adult price attribute.
     */
    public function getFormattedAdultPriceAttribute(): string
    {
        return number_format($this->adult_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted child price attribute.
     */
    public function getFormattedChildPriceAttribute(): string
    {
        return number_format($this->child_price, 2) . ' ' . $this->currency;
    }

    /**
     * Get the formatted infant price attribute.
     */
    public function getFormattedInfantPriceAttribute(): string
    {
        return number_format($this->infant_price, 2) . ' ' . $this->currency;
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
     * Scope to filter by approved tours.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }

    /**
     * Scope to filter by tour type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('adult_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to filter by duration.
     */
    public function scopeByDuration($query, $duration)
    {
        return $query->where('duration', $duration);
    }
}
