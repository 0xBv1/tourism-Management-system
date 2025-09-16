<?php

namespace App\Models;

use App\Traits\Models\Enabled;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string $description
 */
class Room extends Model
{
    use HasSeo, Translatable, Enabled, HasAutoSlug;

    public array $translatedAttributes = [
        'name',
        'description'
    ];

    protected $fillable = [
        'slug',
        'featured_image',
        'banner',
        'hotel_id',
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
    ];

    protected $casts = [
        'gallery' => 'array',
        'enabled' => 'boolean',
        'extra_bed_available' => 'boolean',
        'extra_bed_price' => 'decimal:2',
        'max_extra_beds' => 'integer',
    ];

    protected $hidden = [
        'translations'
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }
}
