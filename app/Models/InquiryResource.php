<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasAuditLog;

// Import all resource models
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Extra;
use App\Models\Ticket;
use App\Models\Dahabia;
use App\Models\Restaurant;

class InquiryResource extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'inquiry_id',
        'resource_type',
        'resource_id',
        'added_by',
        'start_at',
        'end_at',
        'check_in',
        'check_out',
        'number_of_rooms',
        'number_of_adults',
        'number_of_children',
        'rate_per_adult',
        'rate_per_child',
        'price_type',
        'original_price',
        'new_price',
        'increase_percent',
        'effective_price',
        'currency',
        'price_note',
    ];

    protected $casts = [
        'resource_id' => 'integer',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'check_in' => 'date',
        'check_out' => 'date',
        'number_of_rooms' => 'integer',
        'number_of_adults' => 'integer',
        'number_of_children' => 'integer',
        'rate_per_adult' => 'decimal:2',
        'rate_per_child' => 'decimal:2',
        'original_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'increase_percent' => 'decimal:2',
        'effective_price' => 'decimal:2',
    ];

    /**
     * Get the inquiry that owns the resource.
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the user who added this resource.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }


    /**
     * Get the resource model via morphTo relationship.
     */
    public function resource()
    {
        return $this->morphTo('resource', 'resource_type', 'resource_id');
    }

    /**
     * Get the actual resource model based on type and ID.
     */
    public function getActualResource()
    {
        $modelClass = $this->getResourceModelClass();
        return $modelClass::find($this->resource_id);
    }

    /**
     * Get the resource model class based on resource type.
     */
    public function getResourceModelClass(): string
    {
        return match($this->resource_type) {
            'hotel' => Hotel::class,
            'vehicle' => Vehicle::class,
            'guide' => Guide::class,
            'representative' => Representative::class,
            'extra' => Extra::class,
            'ticket' => Ticket::class,
            'dahabia' => Dahabia::class,
            'restaurant' => Restaurant::class,
            default => throw new \InvalidArgumentException("Invalid resource type: {$this->resource_type}")
        };
    }

    /**
     * Get the resource name for display.
     */
    public function getResourceNameAttribute(): string
    {
        if (!$this->resource) {
            return 'Unknown Resource';
        }

        return match($this->resource_type) {
            'hotel' => $this->resource->name,
            'vehicle' => $this->resource->name,
            'guide' => $this->resource->name,
            'representative' => $this->resource->name,
            'extra' => $this->resource->name ?? 'Extra Service',
            'ticket' => $this->resource->name ?? 'Ticket',
            'nile_cruise' => $this->resource->name ?? 'Nile Cruise',
            'dahabia' => $this->resource->name ?? 'Dahabia',
            'restaurant' => $this->resource->name ?? 'Restaurant',
            default => 'Unknown Resource'
        };
    }

    /**
     * Scope to filter by resource type.
     */
    public function scopeByResourceType($query, string $type)
    {
        return $query->where('resource_type', $type);
    }

    /**
     * Scope to filter by inquiry.
     */
    public function scopeForInquiry($query, int $inquiryId)
    {
        return $query->where('inquiry_id', $inquiryId);
    }

    /**
     * Get detailed resource information.
     */
    public function getResourceDetailsAttribute(): array
    {
        $actualResource = $this->getActualResource();
        
        if (!$actualResource) {
            return [
                'name' => 'Unknown Resource',
                'type' => $this->resource_type,
                'details' => []
            ];
        }

        $details = [
            'name' => $actualResource->name ?? 'Unknown',
            'type' => $this->resource_type,
            'city' => $actualResource->city->name ?? null,
            'status' => $actualResource->status ?? null,
        ];

        // Add type-specific details
        switch ($this->resource_type) {
            case 'hotel':
                $details['details'] = [
                    'star_rating' => $actualResource->star_rating ?? null,
                    'total_rooms' => $actualResource->total_rooms ?? null,
                    'available_rooms' => $actualResource->available_rooms ?? null,
                    'price_per_night' => $actualResource->price_per_night ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'address' => $actualResource->address ?? null,
                    'phone' => $actualResource->phone ?? null,
                    'email' => $actualResource->email ?? null,
                ];
                break;
            
            case 'vehicle':
                $details['details'] = [
                    'type' => $actualResource->type ?? null,
                    'brand' => $actualResource->brand ?? null,
                    'model' => $actualResource->model ?? null,
                    'year' => $actualResource->year ?? null,
                    'capacity' => $actualResource->capacity ?? null,
                    'price_per_day' => $actualResource->price_per_day ?? null,
                    'price_per_hour' => $actualResource->price_per_hour ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'license_plate' => $actualResource->license_plate ?? null,
                ];
                break;
            
            case 'guide':
            case 'representative':
                $details['details'] = [
                    'phone' => $actualResource->phone ?? null,
                    'email' => $actualResource->email ?? null,
                    'languages' => $actualResource->languages ?? [],
                    'specialties' => $actualResource->specialties ?? [],
                    'experience_years' => $actualResource->experience_years ?? null,
                    'rating' => $actualResource->rating ?? null,
                    'price_per_hour' => $actualResource->price_per_hour ?? null,
                    'price_per_day' => $actualResource->price_per_day ?? null,
                    'currency' => $actualResource->currency ?? null,
                ];
                break;
            
            case 'extra':
                $details['details'] = [
                    'category' => $actualResource->category ?? null,
                    'description' => $actualResource->description ?? null,
                    'price' => $actualResource->price ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'unit' => $actualResource->unit ?? null,
                ];
                break;

            case 'ticket':
                $details['details'] = [
                    'category' => $actualResource->category ?? null,
                    'description' => $actualResource->description ?? null,
                    'price_per_person' => $actualResource->price_per_person ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'location' => $actualResource->location ?? null,
                ];
                break;

            case 'nile_cruise':
                $details['details'] = [
                    'capacity' => $actualResource->capacity ?? null,
                    'price_per_person' => $actualResource->price_per_person ?? null,
                    'price_per_cabin' => $actualResource->price_per_cabin ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'duration' => $actualResource->duration ?? null,
                ];
                break;

            case 'dahabia':
                $details['details'] = [
                    'capacity' => $actualResource->capacity ?? null,
                    'price_per_person' => $actualResource->price_per_person ?? null,
                    'price_per_charter' => $actualResource->price_per_charter ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'duration' => $actualResource->duration ?? null,
                ];
                break;

            case 'restaurant':
                $details['details'] = [
                    'cuisine_type' => $actualResource->cuisine_type ?? null,
                    'capacity' => $actualResource->capacity ?? null,
                    'currency' => $actualResource->currency ?? null,
                    'location' => $actualResource->location ?? null,
                ];
                break;
        }

        return $details;
    }

    /**
     * Get formatted price information.
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->effective_price) {
            return 'N/A';
        }

        $currency = $this->currency ?? '$';
        $price = number_format($this->effective_price, 2);
        $type = $this->price_type ?? 'day';
        
        return "{$currency} {$price} per {$type}";
    }

    /**
     * Get duration in days if start_at and end_at are set.
     */
    public function getDurationInDaysAttribute(): ?int
    {
        if (!$this->start_at || !$this->end_at) {
            return null;
        }

        return $this->start_at->diffInDays($this->end_at) + 1;
    }

    /**
     * Get total cost based on duration and effective price.
     */
    public function getTotalCostAttribute(): ?float
    {
        if (!$this->effective_price) {
            return null;
        }

        if ($this->resource_type === 'hotel' && $this->check_in && $this->check_out) {
            $nights = $this->check_in->diffInDays($this->check_out);
            $rooms = $this->number_of_rooms ?? 1;
            return $this->effective_price * $nights * $rooms;
        }

        if ($this->duration_in_days && $this->price_type === 'day') {
            return $this->effective_price * $this->duration_in_days;
        }

        if ($this->start_at && $this->end_at && $this->price_type === 'hour') {
            $hours = $this->start_at->diffInHours($this->end_at);
            return $this->effective_price * $hours;
        }

        return $this->effective_price;
    }
}
