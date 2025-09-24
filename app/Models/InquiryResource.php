<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\HasAuditLog;

class InquiryResource extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'inquiry_id',
        'resource_type',
        'resource_id',
        'added_by',
    ];

    protected $casts = [
        'resource_id' => 'integer',
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
     * Get the resource (Hotel, Vehicle, Guide, Representative, Extra).
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource', 'resource_type', 'resource_id');
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
            'extra' => Extra::class, // We'll create this model
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
}
