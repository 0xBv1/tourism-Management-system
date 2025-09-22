<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResourceBooking;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Representative;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function getAvailability(Request $request)
    {
        $resourceType = $request->get('resource_type', 'guide');
        $resourceId = $request->get('resource_id');
        $startDate = $request->get('start', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end', Carbon::now()->endOfMonth()->toDateString());
        $cityId = $request->get('city_id');

        // Get the appropriate model based on resource type
        $model = $this->getModel($resourceType);
        if (!$model) {
            return response()->json(['error' => 'Invalid resource type'], 400);
        }

        // Build query
        $query = $model::with(['city', 'bookings' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q2) use ($startDate, $endDate) {
                  $q2->where('start_date', '<=', $startDate)
                     ->where('end_date', '>=', $endDate);
              });
        }]);

        // Apply filters
        if ($resourceId) {
            $query->where('id', $resourceId);
        }

        if ($cityId) {
            $query->where('city_id', $cityId);
        }

        $resources = $query->get();

        // Generate calendar events
        $events = [];
        foreach ($resources as $resource) {
            // Add resource availability events
            $events = array_merge($events, $this->generateAvailabilityEvents($resource, $startDate, $endDate));
            
            // Add booking events
            $events = array_merge($events, $this->generateBookingEvents($resource, $startDate, $endDate));
        }

        return response()->json($events);
    }

    private function getModel($resourceType)
    {
        return match($resourceType) {
            'guide' => Guide::class,
            'hotel' => Hotel::class,
            'vehicle' => Vehicle::class,
            'representative' => Representative::class,
            default => null,
        };
    }

    private function generateAvailabilityEvents($resource, $startDate, $endDate)
    {
        $events = [];
        $resourceType = strtolower(class_basename($resource));
        
        // Generate availability schedule based on resource type
        if ($resourceType === 'guide' && isset($resource->availability_schedule)) {
            $events = array_merge($events, $this->generateGuideAvailabilityEvents($resource, $startDate, $endDate));
        } else {
            // For other resources, show as available by default
            $events[] = [
                'id' => "availability-{$resource->id}",
                'title' => "{$resource->name} - Available",
                'start' => $startDate,
                'end' => $endDate,
                'color' => '#28a745',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'availability',
                    'resource_id' => $resource->id,
                    'resource_name' => $resource->name,
                    'city' => $resource->city->name ?? 'Unknown',
                    'status' => 'available'
                ]
            ];
        }

        return $events;
    }

    private function generateGuideAvailabilityEvents($guide, $startDate, $endDate)
    {
        $events = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        if (!$guide->availability_schedule) {
            return $events;
        }

        $current = $start->copy();
        while ($current->lte($end)) {
            $dayOfWeek = strtolower($current->format('l'));
            
            if (isset($guide->availability_schedule[$dayOfWeek]) && $guide->availability_schedule[$dayOfWeek]) {
                $events[] = [
                    'id' => "availability-{$guide->id}-{$current->format('Y-m-d')}",
                    'title' => "{$guide->name} - Available",
                    'start' => $current->format('Y-m-d'),
                    'color' => '#28a745',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'availability',
                        'resource_id' => $guide->id,
                        'resource_name' => $guide->name,
                        'city' => $guide->city->name ?? 'Unknown',
                        'status' => 'available'
                    ]
                ];
            } else {
                $events[] = [
                    'id' => "unavailable-{$guide->id}-{$current->format('Y-m-d')}",
                    'title' => "{$guide->name} - Unavailable",
                    'start' => $current->format('Y-m-d'),
                    'color' => '#6c757d',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'type' => 'unavailable',
                        'resource_id' => $guide->id,
                        'resource_name' => $guide->name,
                        'city' => $guide->city->name ?? 'Unknown',
                        'status' => 'unavailable'
                    ]
                ];
            }
            
            $current->addDay();
        }

        return $events;
    }

    private function generateBookingEvents($resource, $startDate, $endDate)
    {
        $events = [];
        $resourceType = strtolower(class_basename($resource));
        
        foreach ($resource->bookings as $booking) {
            $events[] = [
                'id' => "booking-{$booking->id}",
                'title' => "{$resource->name} - Booked",
                'start' => $booking->start_date->format('Y-m-d'),
                'end' => $booking->end_date->addDay()->format('Y-m-d'),
                'color' => $this->getBookingColor($booking->status),
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'booking',
                    'booking_id' => $booking->id,
                    'resource_id' => $resource->id,
                    'resource_name' => $resource->name,
                    'city' => $resource->city->name ?? 'Unknown',
                    'status' => strtolower($booking->status->value),
                    'quantity' => $booking->quantity,
                    'total_price' => $booking->total_price,
                    'currency' => $booking->currency,
                    'notes' => $booking->notes
                ]
            ];
        }

        return $events;
    }

    private function getBookingColor($status)
    {
        return match($status->value) {
            'OCCUPIED' => '#dc3545',
            'MAINTENANCE' => '#ffc107',
            'CANCELLED' => '#6c757d',
            default => '#007bff',
        };
    }
}
