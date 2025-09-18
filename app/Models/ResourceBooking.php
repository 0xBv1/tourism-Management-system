<?php

namespace App\Models;

use App\Enums\ResourceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ResourceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_file_id',
        'resource_type',
        'resource_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'quantity',
        'unit_price',
        'total_price',
        'currency',
        'status',
        'notes',
        'special_requirements',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'status' => ResourceStatus::class,
        'special_requirements' => 'array',
    ];

    public function bookingFile(): BelongsTo
    {
        return $this->belongsTo(BookingFile::class);
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByResourceType($query, $type)
    {
        return $query->where('resource_type', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [ResourceStatus::AVAILABLE, ResourceStatus::OCCUPIED]);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->getLabel();
    }

    public function getDurationInDaysAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getDurationInHoursAttribute(): int
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }
        
        return $this->start_time->diffInHours($this->end_time);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [ResourceStatus::AVAILABLE, ResourceStatus::OCCUPIED]);
    }

    public function isOverlapping($startDate, $endDate, $excludeId = null): bool
    {
        $query = static::where('resource_type', $this->resource_type)
            ->where('resource_id', $this->resource_id)
            ->where('id', '!=', $excludeId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate)
                         ->where('end_date', '>=', $endDate);
                  });
            });

        return $query->exists();
    }
}




