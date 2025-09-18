<?php

namespace App\Services;

use App\Models\BookingFile;
use App\Models\ResourceBooking;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Enums\ResourceStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ResourceAssignmentService
{
    /**
     * Assign a resource to a booking file
     */
    public function assignResource(
        BookingFile $bookingFile,
        string $resourceType,
        int $resourceId,
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null,
        int $quantity = 1,
        ?float $unitPrice = null,
        ?string $currency = null,
        ?array $specialRequirements = null,
        ?string $notes = null
    ): ResourceBooking {
        // Validate resource exists and is available
        $resource = $this->getResource($resourceType, $resourceId);
        $this->validateResourceAvailability($resource, $resourceType, $startDate, $endDate, $startTime, $endTime);

        // Calculate total price if not provided
        if ($unitPrice === null) {
            $unitPrice = $this->calculateUnitPrice($resource, $resourceType, $startDate, $endDate, $startTime, $endTime);
        }

        $totalPrice = $unitPrice * $quantity * $this->calculateDuration($startDate, $endDate, $startTime, $endTime);

        // Create resource booking
        $resourceBooking = ResourceBooking::create([
            'booking_file_id' => $bookingFile->id,
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'currency' => $currency ?? $bookingFile->currency,
            'status' => ResourceStatus::OCCUPIED,
            'special_requirements' => $specialRequirements,
            'notes' => $notes,
        ]);

        // Update resource availability
        $this->updateResourceAvailability($resource, $resourceType, $quantity, false);

        return $resourceBooking;
    }

    /**
     * Unassign a resource from a booking file
     */
    public function unassignResource(ResourceBooking $resourceBooking): bool
    {
        $resource = $this->getResource($resourceBooking->resource_type, $resourceBooking->resource_id);
        
        // Update resource availability
        $this->updateResourceAvailability($resource, $resourceBooking->resource_type, $resourceBooking->quantity, true);
        
        // Delete the resource booking
        return $resourceBooking->delete();
    }

    /**
     * Check resource availability for a given period
     */
    public function checkAvailability(
        string $resourceType,
        int $resourceId,
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null,
        ?int $excludeBookingId = null
    ): bool {
        $resource = $this->getResource($resourceType, $resourceId);
        
        if (!$resource->isAvailable()) {
            return false;
        }

        // Check for overlapping bookings
        $overlapping = ResourceBooking::where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->where('id', '!=', $excludeBookingId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                             ->where('end_date', '>=', $endDate);
                      });
            })
            ->exists();

        return !$overlapping;
    }

    /**
     * Get available resources for a given period
     */
    public function getAvailableResources(
        string $resourceType,
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null,
        ?int $cityId = null,
        ?array $filters = []
    ): Collection {
        $query = $this->getResourceQuery($resourceType)
            ->available();

        if ($cityId) {
            $query->byCity($cityId);
        }

        // Apply additional filters
        if (!empty($filters)) {
            $query = $this->applyFilters($query, $resourceType, $filters);
        }

        $resources = $query->get();

        // Filter by availability
        return $resources->filter(function ($resource) use ($resourceType, $startDate, $endDate, $startTime, $endTime) {
            return $this->checkAvailability($resourceType, $resource->id, $startDate, $endDate, $startTime, $endTime);
        });
    }

    /**
     * Get resource utilization statistics
     */
    public function getResourceUtilization(
        string $resourceType,
        int $resourceId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        $resource = $this->getResource($resourceType, $resourceId);
        
        $bookings = ResourceBooking::where('resource_type', $resourceType)
            ->where('resource_id', $resourceId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->get();

        $totalDays = $startDate->diffInDays($endDate) + 1;
        $bookedDays = 0;

        foreach ($bookings as $booking) {
            $bookingStart = max($booking->start_date, $startDate);
            $bookingEnd = min($booking->end_date, $endDate);
            $bookedDays += $bookingStart->diffInDays($bookingEnd) + 1;
        }

        $utilizationPercentage = $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0;

        return [
            'resource' => $resource,
            'total_days' => $totalDays,
            'booked_days' => $bookedDays,
            'utilization_percentage' => $utilizationPercentage,
            'bookings_count' => $bookings->count(),
            'total_revenue' => $bookings->sum('total_price'),
        ];
    }

    /**
     * Get resource by type and ID
     */
    private function getResource(string $resourceType, int $resourceId): Model
    {
        $resourceClass = match ($resourceType) {
            'hotel' => Hotel::class,
            'vehicle' => Vehicle::class,
            'guide' => Guide::class,
            'representative' => Representative::class,
            default => throw new \InvalidArgumentException("Invalid resource type: {$resourceType}")
        };

        return $resourceClass::findOrFail($resourceId);
    }

    /**
     * Get resource query based on type
     */
    private function getResourceQuery(string $resourceType)
    {
        return match ($resourceType) {
            'hotel' => Hotel::query(),
            'vehicle' => Vehicle::query(),
            'guide' => Guide::query(),
            'representative' => Representative::query(),
            default => throw new \InvalidArgumentException("Invalid resource type: {$resourceType}")
        };
    }

    /**
     * Validate resource availability
     */
    private function validateResourceAvailability(
        Model $resource,
        string $resourceType,
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null
    ): void {
        if (!$resource->isAvailable()) {
            throw new \Exception("Resource is not available");
        }

        if (!$this->checkAvailability($resourceType, $resource->id, $startDate, $endDate, $startTime, $endTime)) {
            throw new \Exception("Resource is not available for the selected period");
        }

        // Special validation for hotels
        if ($resourceType === 'hotel' && $resource instanceof Hotel) {
            if ($resource->available_rooms <= 0) {
                throw new \Exception("No rooms available");
            }
        }
    }

    /**
     * Calculate unit price for a resource
     */
    private function calculateUnitPrice(
        Model $resource,
        string $resourceType,
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null
    ): float {
        $duration = $this->calculateDuration($startDate, $endDate, $startTime, $endTime);

        return match ($resourceType) {
            'hotel' => $resource->price_per_night,
            'vehicle' => $startTime && $endTime ? $resource->price_per_hour : $resource->price_per_day,
            'guide' => $startTime && $endTime ? $resource->price_per_hour : $resource->price_per_day,
            'representative' => $startTime && $endTime ? $resource->price_per_hour : $resource->price_per_day,
            default => 0
        };
    }

    /**
     * Calculate duration in appropriate units
     */
    private function calculateDuration(
        Carbon $startDate,
        Carbon $endDate,
        ?Carbon $startTime = null,
        ?Carbon $endTime = null
    ): int {
        if ($startTime && $endTime) {
            return $startTime->diffInHours($endTime);
        }
        
        return $startDate->diffInDays($endDate) + 1;
    }

    /**
     * Update resource availability
     */
    private function updateResourceAvailability(
        Model $resource,
        string $resourceType,
        int $quantity,
        bool $increase
    ): void {
        if ($resourceType === 'hotel' && $resource instanceof Hotel) {
            $change = $increase ? $quantity : -$quantity;
            $resource->updateAvailableRooms($change);
        }
    }

    /**
     * Apply filters to resource query
     */
    private function applyFilters($query, string $resourceType, array $filters)
    {
        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            switch ($key) {
                case 'min_price':
                    $priceColumn = $resourceType === 'hotel' ? 'price_per_night' : 'price_per_day';
                    $query->where($priceColumn, '>=', $value);
                    break;
                case 'max_price':
                    $priceColumn = $resourceType === 'hotel' ? 'price_per_night' : 'price_per_day';
                    $query->where($priceColumn, '<=', $value);
                    break;
                case 'capacity':
                    if ($resourceType === 'vehicle') {
                        $query->byCapacity($value);
                    }
                    break;
                case 'star_rating':
                    if ($resourceType === 'hotel') {
                        $query->byStarRating($value);
                    }
                    break;
                case 'language':
                    if (in_array($resourceType, ['guide', 'representative'])) {
                        $query->byLanguage($value);
                    }
                    break;
                case 'specialization':
                    if (in_array($resourceType, ['guide', 'representative'])) {
                        $query->bySpecialization($value);
                    }
                    break;
            }
        }

        return $query;
    }
}




