<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ResourceAssignmentService;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\ResourceBooking;
use App\Models\InquiryResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceReportController extends Controller
{
    protected $resourceAssignmentService;

    public function __construct(ResourceAssignmentService $resourceAssignmentService)
    {
        $this->resourceAssignmentService = $resourceAssignmentService;
    }

    /**
     * Show resource utilization reports
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $hotelUtilization = $this->getResourceUtilizationReport('hotel', $startDate, $endDate);
        $vehicleUtilization = $this->getResourceUtilizationReport('vehicle', $startDate, $endDate);
        $guideUtilization = $this->getResourceUtilizationReport('guide', $startDate, $endDate);
        $representativeUtilization = $this->getResourceUtilizationReport('representative', $startDate, $endDate);

        $overallStats = $this->getOverallStats($startDate, $endDate);
        
        // Enhanced analytics
        $bookingPatterns = $this->getBookingPatterns($startDate, $endDate);
        $resourcePerformance = $this->getResourcePerformanceMetrics($startDate, $endDate);
        $monthlyTrends = $this->getMonthlyTrends($startDate, $endDate);
        $topPerformers = $this->getTopPerformingResources($startDate, $endDate);

        return view('dashboard.reports.resource-utilization', compact(
            'hotelUtilization',
            'vehicleUtilization', 
            'guideUtilization',
            'representativeUtilization',
            'overallStats',
            'bookingPatterns',
            'resourcePerformance',
            'monthlyTrends',
            'topPerformers',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get utilization report for a specific resource type
     */
    public function getResourceUtilization(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|in:hotel,vehicle,guide,representative',
            'resource_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $utilization = $this->resourceAssignmentService->getResourceUtilization(
            $request->resource_type,
            $request->resource_id,
            $startDate,
            $endDate
        );

        return response()->json($utilization);
    }

    /**
     * Get detailed utilization report for a specific resource
     */
    public function showResourceDetails(Request $request, $resourceType, $resourceId)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $resource = $this->getResource($resourceType, $resourceId);
        $utilization = $this->resourceAssignmentService->getResourceUtilization(
            $resourceType,
            $resourceId,
            $startDate,
            $endDate
        );

        $bookings = $resource->bookings()
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->with('bookingFile.inquiry')
            ->orderBy('start_date')
            ->get();

        return view('dashboard.reports.resource-details', compact(
            'resource',
            'utilization',
            'bookings',
            'startDate',
            'endDate',
            'resourceType'
        ));
    }

    /**
     * Export utilization report
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $hotelUtilization = $this->getResourceUtilizationReport('hotel', $startDate, $endDate);
        $vehicleUtilization = $this->getResourceUtilizationReport('vehicle', $startDate, $endDate);
        $guideUtilization = $this->getResourceUtilizationReport('guide', $startDate, $endDate);
        $representativeUtilization = $this->getResourceUtilizationReport('representative', $startDate, $endDate);

        // Generate CSV content
        $csv = $this->generateResourceUtilizationCSV(
            $hotelUtilization,
            $vehicleUtilization,
            $guideUtilization,
            $representativeUtilization,
            $startDate,
            $endDate
        );

        $filename = 'resource_utilization_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate CSV content for resource utilization report
     */
    private function generateResourceUtilizationCSV(
        array $hotelUtilization,
        array $vehicleUtilization,
        array $guideUtilization,
        array $representativeUtilization,
        Carbon $startDate,
        Carbon $endDate
    ): string {
        $csv = "Resource Utilization Report\n";
        $csv .= "Period: {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";

        // Overall Summary
        $csv .= "OVERALL SUMMARY\n";
        $csv .= "Resource Type,Total Resources,Avg Utilization %,Total Bookings,Total Revenue\n";
        
        $csv .= "Hotels," . count($hotelUtilization) . "," . 
            (count($hotelUtilization) > 0 ? round(collect($hotelUtilization)->avg('utilization_percentage'), 2) : 0) . "," .
            collect($hotelUtilization)->sum('bookings_count') . "," .
            collect($hotelUtilization)->sum('total_revenue') . "\n";
            
        $csv .= "Vehicles," . count($vehicleUtilization) . "," . 
            (count($vehicleUtilization) > 0 ? round(collect($vehicleUtilization)->avg('utilization_percentage'), 2) : 0) . "," .
            collect($vehicleUtilization)->sum('bookings_count') . "," .
            collect($vehicleUtilization)->sum('total_revenue') . "\n";
            
        $csv .= "Guides," . count($guideUtilization) . "," . 
            (count($guideUtilization) > 0 ? round(collect($guideUtilization)->avg('utilization_percentage'), 2) : 0) . "," .
            collect($guideUtilization)->sum('bookings_count') . "," .
            collect($guideUtilization)->sum('total_revenue') . "\n";
            
        $csv .= "Representatives," . count($representativeUtilization) . "," . 
            (count($representativeUtilization) > 0 ? round(collect($representativeUtilization)->avg('utilization_percentage'), 2) : 0) . "," .
            collect($representativeUtilization)->sum('bookings_count') . "," .
            collect($representativeUtilization)->sum('total_revenue') . "\n\n";

        // Hotel Details
        $csv .= "HOTEL UTILIZATION DETAILS\n";
        $csv .= "Hotel Name,City,Star Rating,Total Rooms,Available Rooms,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue,Price Per Night\n";
        foreach ($hotelUtilization as $util) {
            $hotel = $util['resource'];
            $csv .= "\"{$hotel->name}\",\"{$hotel->city->name}\",{$hotel->star_rating},{$hotel->total_rooms},{$hotel->available_rooms}," .
                "{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']},{$hotel->price_per_night}\n";
        }
        $csv .= "\n";

        // Vehicle Details
        $csv .= "VEHICLE UTILIZATION DETAILS\n";
        $csv .= "Vehicle Name,Type,Brand,Model,Capacity,City,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue,Price Per Hour\n";
        foreach ($vehicleUtilization as $util) {
            $vehicle = $util['resource'];
            $csv .= "\"{$vehicle->name}\",{$vehicle->type},{$vehicle->brand},{$vehicle->model},{$vehicle->capacity},\"{$vehicle->city->name}\"," .
                "{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']},{$vehicle->price_per_hour}\n";
        }
        $csv .= "\n";

        // Guide Details
        $csv .= "GUIDE UTILIZATION DETAILS\n";
        $csv .= "Guide Name,City,Languages,Specialties,Rating,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue,Price Per Hour\n";
        foreach ($guideUtilization as $util) {
            $guide = $util['resource'];
            $languages = is_string($guide->languages) ? $guide->languages : json_encode($guide->languages);
            $specialties = is_string($guide->specialties) ? $guide->specialties : json_encode($guide->specialties);
            $csv .= "\"{$guide->name}\",\"{$guide->city->name}\",\"{$languages}\",\"{$specialties}\",{$guide->rating}," .
                "{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']},{$guide->price_per_hour}\n";
        }
        $csv .= "\n";

        // Representative Details
        $csv .= "REPRESENTATIVE UTILIZATION DETAILS\n";
        $csv .= "Representative Name,City,Languages,Specialties,Rating,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue,Price Per Hour\n";
        foreach ($representativeUtilization as $util) {
            $representative = $util['resource'];
            $languages = is_string($representative->languages) ? $representative->languages : json_encode($representative->languages);
            $specialties = is_string($representative->specialties) ? $representative->specialties : json_encode($representative->specialties);
            $csv .= "\"{$representative->name}\",\"{$representative->city->name}\",\"{$languages}\",\"{$specialties}\",{$representative->rating}," .
                "{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']},{$representative->price_per_hour}\n";
        }

        return $csv;
    }

    /**
     * Get resource utilization report for a specific type
     */
    private function getResourceUtilizationReport(string $resourceType, Carbon $startDate, Carbon $endDate): array
    {
        $resources = $this->getResourceQuery($resourceType)->get();
        $utilizationData = [];

        foreach ($resources as $resource) {
            $utilization = $this->resourceAssignmentService->getResourceUtilization(
                $resourceType,
                $resource->id,
                $startDate,
                $endDate
            );

            $utilizationData[] = [
                'resource' => $resource,
                'utilization_percentage' => $utilization['utilization_percentage'],
                'total_days' => $utilization['total_days'],
                'booked_days' => $utilization['booked_days'],
                'bookings_count' => $utilization['bookings_count'],
                'total_revenue' => $utilization['total_revenue'],
            ];
        }

        // Sort by utilization percentage (descending)
        usort($utilizationData, function ($a, $b) {
            return $b['utilization_percentage'] <=> $a['utilization_percentage'];
        });

        return $utilizationData;
    }

    /**
     * Get overall statistics
     */
    private function getOverallStats(Carbon $startDate, Carbon $endDate): array
    {
        $totalResources = Hotel::count() + Vehicle::count() + Guide::count() + Representative::count();
        
        $totalBookings = \App\Models\ResourceBooking::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->count();

        $totalRevenue = \App\Models\ResourceBooking::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->sum('total_price');

        return [
            'total_resources' => $totalResources,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'period_days' => $startDate->diffInDays($endDate) + 1,
        ];
    }

    /**
     * Get resource by type and ID
     */
    private function getResource(string $resourceType, int $resourceId)
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
     * Get booking patterns analysis
     */
    private function getBookingPatterns(Carbon $startDate, Carbon $endDate): array
    {
        $bookings = ResourceBooking::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->get();

        // Booking duration patterns
        $durationPatterns = $bookings->groupBy(function($booking) {
            $days = $booking->start_date->diffInDays($booking->end_date) + 1;
            if ($days <= 1) return '1 day';
            if ($days <= 3) return '2-3 days';
            if ($days <= 7) return '4-7 days';
            if ($days <= 14) return '8-14 days';
            return '15+ days';
        })->map->count();

        // Peak booking days (day of week)
        $dayOfWeekPatterns = $bookings->groupBy(function($booking) {
            return $booking->start_date->dayOfWeek;
        })->map->count();

        // Average booking value by resource type
        $avgBookingValue = $bookings->groupBy('resource_type')
            ->map(function($group) {
                return [
                    'count' => $group->count(),
                    'avg_value' => $group->avg('total_price'),
                    'total_value' => $group->sum('total_price')
                ];
            });

        return [
            'duration_patterns' => $durationPatterns,
            'day_of_week_patterns' => $dayOfWeekPatterns,
            'avg_booking_value' => $avgBookingValue,
            'total_bookings' => $bookings->count(),
            'total_revenue' => $bookings->sum('total_price')
        ];
    }

    /**
     * Get resource performance metrics
     */
    private function getResourcePerformanceMetrics(Carbon $startDate, Carbon $endDate): array
    {
        $resourceTypes = ['hotel', 'vehicle', 'guide', 'representative'];
        $metrics = [];

        foreach ($resourceTypes as $type) {
            $resources = $this->getResourceQuery($type)->get();
            $bookings = ResourceBooking::where('resource_type', $type)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                })
                ->get();

            $totalUtilization = 0;
            $totalRevenue = $bookings->sum('total_price');
            $avgBookingValue = $bookings->count() > 0 ? $bookings->avg('total_price') : 0;

            foreach ($resources as $resource) {
                $utilization = $this->resourceAssignmentService->getResourceUtilization(
                    $type,
                    $resource->id,
                    $startDate,
                    $endDate
                );
                $totalUtilization += $utilization['utilization_percentage'];
            }

            $metrics[$type] = [
                'total_resources' => $resources->count(),
                'avg_utilization' => $resources->count() > 0 ? round($totalUtilization / $resources->count(), 2) : 0,
                'total_bookings' => $bookings->count(),
                'total_revenue' => $totalRevenue,
                'avg_booking_value' => round($avgBookingValue, 2),
                'booking_frequency' => $resources->count() > 0 ? round($bookings->count() / $resources->count(), 2) : 0
            ];
        }

        return $metrics;
    }

    /**
     * Get monthly trends for the past 6 months
     */
    private function getMonthlyTrends(Carbon $startDate, Carbon $endDate): array
    {
        $trends = [];
        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate->lte($endDate)) {
            $monthStart = $currentDate->copy()->startOfMonth();
            $monthEnd = $currentDate->copy()->endOfMonth();

            $bookings = ResourceBooking::whereBetween('start_date', [$monthStart, $monthEnd])
                ->orWhereBetween('end_date', [$monthStart, $monthEnd])
                ->orWhere(function ($query) use ($monthStart, $monthEnd) {
                    $query->where('start_date', '<=', $monthStart)
                          ->where('end_date', '>=', $monthEnd);
                })
                ->get();

            $trends[] = [
                'month' => $currentDate->format('M Y'),
                'bookings' => $bookings->count(),
                'revenue' => $bookings->sum('total_price'),
                'avg_booking_value' => $bookings->count() > 0 ? round($bookings->avg('total_price'), 2) : 0
            ];

            $currentDate->addMonth();
        }

        return $trends;
    }

    /**
     * Get top performing resources across all types
     */
    private function getTopPerformingResources(Carbon $startDate, Carbon $endDate): array
    {
        $topPerformers = [];
        $resourceTypes = ['hotel', 'vehicle', 'guide', 'representative'];

        foreach ($resourceTypes as $type) {
            $resources = $this->getResourceQuery($type)->get();
            
            foreach ($resources as $resource) {
                $utilization = $this->resourceAssignmentService->getResourceUtilization(
                    $type,
                    $resource->id,
                    $startDate,
                    $endDate
                );

                $bookings = ResourceBooking::where('resource_type', $type)
                    ->where('resource_id', $resource->id)
                    ->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                    })
                    ->get();

                $topPerformers[] = [
                    'resource' => $resource,
                    'resource_type' => $type,
                    'utilization_percentage' => $utilization['utilization_percentage'],
                    'bookings_count' => $bookings->count(),
                    'total_revenue' => $bookings->sum('total_price'),
                    'avg_booking_value' => $bookings->count() > 0 ? round($bookings->avg('total_price'), 2) : 0
                ];
            }
        }

        // Sort by utilization percentage and take top 10
        usort($topPerformers, function ($a, $b) {
            return $b['utilization_percentage'] <=> $a['utilization_percentage'];
        });

        return array_slice($topPerformers, 0, 10);
    }
}




