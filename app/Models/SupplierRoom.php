<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Translations\SupplierRoomTranslation;

/**
 * @property string $name
 * @property string $description
 */
class SupplierRoom extends Model
{
    use HasSeo, Translatable, Enabled, HasAutoSlug;

    public array $translatedAttributes = [
        'name',
        'description'
    ];

    protected string $translationModel = SupplierRoomTranslation::class;

    protected $fillable = [
        'featured_image',
        'banner',
        'supplier_hotel_id',
        'gallery',
        'enabled',
        'bed_count',
        'room_type',
        'max_capacity',
        'bed_types',
        'night_price',
        'extra_bed_available',
        'extra_bed_price',
        'max_extra_beds',
        'extra_bed_description',
        'approved',
        'rejection_reason',
    ];

    protected $casts = [
        'gallery' => 'array',
        'enabled' => 'boolean',
        'extra_bed_available' => 'boolean',
        'extra_bed_price' => 'decimal:2',
        'max_extra_beds' => 'integer',
        'approved' => 'boolean',
    ];

    protected $hidden = [
        'translations'
    ];

    protected $appends = [
        'status_label',
        'status_color',
    ];

    public function supplierHotel(): BelongsTo
    {
        return $this->belongsTo(SupplierHotel::class, 'supplier_hotel_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'supplier_room_amenities');
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
     * Scope to filter by approved rooms.
     */
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
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

    /**
     * Generate a unique slug from the given text for the current model instance.
     */
    public function generateUniqueSlug(string $text): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($text);
        $slug = $baseSlug;
        $counter = 1;

        // Check if slug already exists and make it unique
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
