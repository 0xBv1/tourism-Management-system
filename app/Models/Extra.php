<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasAuditLog;

class Extra extends Model
{
    use HasFactory, SoftDeletes, HasAuditLog;

    protected $fillable = [
        'name',
        'description',
        'price',
        'currency',
        'category',
        'active',
        'enabled',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'enabled' => 'boolean',
        'features' => 'array', // Will be automatically handled by Laravel's array casting
    ];

    /**
     * Get the bookings for this extra.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ResourceBooking::class, 'resource_id')
            ->where('resource_type', 'extra');
    }

    /**
     * Scope to get only active extras.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)->where('enabled', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by price range.
     */
    public function scopeByPriceRange($query, float $minPrice, float $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Get formatted price with currency.
     */
    public function getFormattedPriceAttribute(): string
    {
        return $this->currency . ' ' . number_format($this->price, 2);
    }

    /**
     * Check if extra is available.
     */
    public function isAvailable(): bool
    {
        return $this->active && $this->enabled;
    }
}
