<?php

namespace App\Models;

use App\Enums\BlogStatus;
use App\Traits\Models\Activated;
use App\Traits\Models\AutoTranslate;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\HasSeo;
use App\Traits\Models\SiteUrl;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $title
 * @property string $description
 * @property string $slug
 * @property string $tags
 */
class Blog extends Model
{
    use HasSeo, Translatable, SoftDeletes, Activated, AutoTranslate, SiteUrl, HasAutoSlug;

    protected $fillable = [
        'featured_image',
        'gallery',
        'active',
        'published_at',
        'status',
        'published_by_id',
        'slug',
        'display_order',
    ];

    public array $translatedAttributes = [
        'title',
        'description',
        'tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'active' => 'boolean',
        'gallery' => 'array',
    ];

    protected $hidden = [
        'translated_at',
        'published_by_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        // Call trait boot methods
        static::bootHasAutoSlug();

        $segments = collect(request()->segments());

        if ($segments->isNotEmpty() && $segments->first() == 'api') {
            static::addGlobalScope('published', fn($q) => $q->where('status', BlogStatus::PUBLISHED->value));
        }
    }



    public function published_by(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_blog_categories',
            'blog_id', 'blog_category_id');
    }

    public function relatedTours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'blog_related_tours');
    }
}
