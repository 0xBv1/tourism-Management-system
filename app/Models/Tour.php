<?php

namespace App\Models;

use App\Traits\Models\AutoTranslate;
use App\Traits\Models\Enabled;
use App\Traits\Models\HasSeo;
use App\Traits\Models\HasAutoSlug;
use App\Traits\Models\SiteUrl;
use Astrotomic\Translatable\Translatable;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property string $title
 * @property string $slug
 * @property string $overview
 * @property string $highlights
 * @property string $included
 * @property string $excluded
 * @property string $type
 * @property string $run
 * @property string $pickup_time
 * @property string $duration
 * @property string $link
 */
class Tour extends Model
{
    use HasFactory, Translatable, SoftDeletes, HasSeo, AutoTranslate, Enabled, HasAutoSlug, SiteUrl;

    public array $translatedAttributes = [
        'title',
        'overview',
        'highlights',
        'excluded',
        'included',
        'duration',
        'type',
        'run',
        'pickup_time',
    ];

    protected $fillable = [
        'enabled',
        'slug',
        'display_order',
        'code',
        'featured',
        'featured_image',
        'gallery',
        'adult_price',
        'child_price',
        'pricing_groups',
        'duration_in_days',
        'infant_price',
        'available'
    ];

    protected $casts = [
        'display_order' => 'integer',
        'enabled' => 'boolean',
        'duration_in_days' => 'integer',
        'featured' => 'boolean',
        'gallery' => 'array',
        'pricing_groups' => 'collection',
        'translated_at' => 'datetime',
        'available' => 'array',
    ];

    protected $hidden = [
        'translated_at',
        'rates',
        'deleted_at',
        'available',
        'created_at',
        'updated_at'
    ];

    protected $appends = ['start_from', 'calender_availability', 'rate', 'overview_text'];

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'tour_destinations');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'tour_categories');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(TourOption::class, 'tour_option_tours');
    }

    public function durations(): BelongsToMany
    {
        return $this->belongsToMany(Duration::class, 'tour_durations');
    }

    public function days(): HasMany
    {
        return $this->hasMany(TourDay::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(TourSeason::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TourReview::class);
    }

    public function wishlisted(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_tour_wishlist')
            ->where('client_id', auth('client')->id());
    }

    public function pricingGroups(): Attribute
    {
        return new Attribute(
            get: fn($value) => collect(json_decode($value, true))->map(fn($group) => [
                'from' => (int)$group['from'],
                'to' => (int)$group['to'],
                'price' => (float)$group['price'],
                'child_price' => (float)($group['child_price'] ?? 0),
            ])
        );
    }

    public function startFrom(): Attribute
    {
        return new Attribute(
            get: fn() => $this->isEmptyPricingGroups() ?
                $this->adult_price :
                min($this->pricing_groups->min('price'), $this->adult_price)
        );
    }

    public function overviewText(): Attribute
    {
        return new Attribute(
            get: fn() => Str::of($this->overview)->replace([PHP_EOL, '\\n', '\\t', '\\r'], '')
                ->stripTags()
                //->htmlEntityDecode()
                ->trim()
                ->toString()
        );
    }

    public function rate(): Attribute
    {
        return new Attribute(
            get: fn() => $this->reviews_number == 0 ? 0 : (float)number_format($this->rates / $this->reviews_number, 1)
        );
    }

    public function link(): Attribute
    {
        return new Attribute(
            get: fn() => site_url('/tour/' . $this->slug)
        );
    }

    public function isEmptyPricingGroups(): bool
    {
        if (!$this->pricing_groups) {
            $this->pricing_groups = collect([]);
        }
        if ($this->pricing_groups->count() == 1) {
            $firstGroup = $this->pricing_groups->first();
            return !$firstGroup['from'] && !$firstGroup['to'] && !$firstGroup['price'];
        }
        return false;
    }

    public function calcAdultPrice($adults, $date = null)
    {
        if ($date) {
            $date = Carbon::parse($date);
            foreach ($this->seasons as $season) {
                $calender_availability = $season->calender_availability;
                if (in_array(strtolower($date->dayName), $calender_availability['day_names']) &&
                    in_array(strtolower($date->monthName), $calender_availability['month_names']) &&
                    in_array($date->day, $calender_availability['day_numbers']) &&
                    in_array($date->year, $calender_availability['years_numbers'])) {
                    $group = $season->pricing_groups->filter(fn($group) => $adults >= $group['from'] && $adults <= $group['to'])
                        ->first();

                    if ($group['price']) {
                        return $group['price'];
                    }
                }
            }
        }

        if (!isset($group)) {
            $group = $this->pricing_groups
                ->filter(fn($group) => $adults >= $group['from'] && $adults <= $group['to'])
                ->first();
        }

        return $group['price'] ?? $this->adult_price;
    }

    public function calcChildPrice($adults, $date = null)
    {
        if ($date) {
            $date = Carbon::parse($date);
            foreach ($this->seasons as $season) {
                $calender_availability = $season->calender_availability;
                if (in_array(strtolower($date->dayName), $calender_availability['day_names']) &&
                    in_array(strtolower($date->monthName), $calender_availability['month_names']) &&
                    in_array($date->day, $calender_availability['day_numbers']) &&
                    in_array($date->year, $calender_availability['years_numbers'])) {
                    $group = $season->pricing_groups->filter(fn($group) => $adults >= $group['from'] && $adults <= $group['to'])
                        ->first();

                    if ($group['child_price']) {
                        return $group['child_price'];
                    }
                }
            }
        }

        if (!isset($group)) {
            $group = $this->pricing_groups
                ->filter(fn($group) => $adults >= $group['from'] && $adults <= $group['to'])
                ->first();
        }

        return $group['child_price'] ?? $this->child_price;
    }

    public function asAttachment(Booking $booking): ?string
    {
        try {
            $basePath = storage_path('app/public/attachments/bookings/' . today()->year . '/' . today()->month . '/' . $booking->id);
            \File::ensureDirectoryExists($basePath);
            $path = $basePath . '/' . $this->slug . '.pdf';
            $pdf = Pdf::loadView('emails.attachments.tour', ['tour' => $this]);
            $pdf->setPaper('a4', 'landscape')->setWarnings(false)->save($path);
            return $path;
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    public function calenderAvailability(): Attribute
    {
        $available = $this->available && $this->available !== 'null' ? json_decode($this->available, true) : null;
        return new Attribute(
            get: function () use ($available) {
                $calender = ['day_numbers' => [], 'day_names' => [], 'month_names' => [], 'years' => []];
                if (!$available) {
                    return $calender;
                }

                $calender['day_numbers'] = collect($available['Days'] ?? [])->keys()->map(fn($day) => ((int)$day))->values()->toArray();
                $calender['years'] = collect($available['Year'] ?? [])->keys()->map(fn($year) => intval($year))->values()->toArray();
                $calender['day_names'] = collect($available['Week'] ?? [])->keys()->map(fn($item) => str($item)->lower())->values()->toArray();
                $calender['month_names'] = collect($available['Month'] ?? [])->keys()->map(fn($item) => str($item)->lower())->values()->toArray();

                return $calender;
            }
        );
    }

    public function isAvailableAtDate($date): bool
    {
        return true;
    }
}
