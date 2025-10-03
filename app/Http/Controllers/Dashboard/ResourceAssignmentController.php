<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BookingFile;
use App\Models\ResourceBooking;
use App\Services\ResourceAssignmentService;
use App\Http\Requests\Dashboard\ResourceAssignmentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceAssignmentController extends Controller
{
    protected $resourceAssignmentService;

    public function __construct(ResourceAssignmentService $resourceAssignmentService)
    {
        $this->resourceAssignmentService = $resourceAssignmentService;
    }

    /**
     * Show resource assignment form for a booking file
     */
    public function create(BookingFile $bookingFile)
    {
        $bookingFile->load('inquiry');
        return view('dashboard.bookings.assign-resources', compact('bookingFile'));
    }

    /**
     * Assign a resource to a booking file
     */
    public function store(ResourceAssignmentRequest $request, BookingFile $bookingFile)
    {
        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
            $startTime = $request->start_time ? Carbon::parse($request->start_time) : null;
            $endTime = $request->end_time ? Carbon::parse($request->end_time) : null;

            $resourceBooking = $this->resourceAssignmentService->assignResource(
                $bookingFile,
                $request->resource_type,
                $request->resource_id,
                $startDate,
                $endDate,
                $startTime,
                $endTime,
                $request->quantity ?? 1,
                $request->unit_price,
                $request->currency ?? $bookingFile->currency,
                $request->special_requirements,
                $request->notes
            );

            session()->flash('message', 'Resource assigned successfully!');
            session()->flash('type', 'success');
            
            return redirect()->route('dashboard.bookings.show', $bookingFile);
        } catch (\Exception $e) {
            session()->flash('message', 'Error assigning resource: ' . $e->getMessage());
            session()->flash('type', 'error');
            
            return back()->withInput();
        }
    }

    /**
     * Remove a resource assignment
     */
    public function destroy(ResourceBooking $resourceBooking)
    {
        try {
            $bookingFile = $resourceBooking->bookingFile;
            
            $this->resourceAssignmentService->unassignResource($resourceBooking);

            session()->flash('message', 'Resource assignment removed successfully!');
            session()->flash('type', 'success');
            
            return redirect()->route('dashboard.bookings.show', $bookingFile);
        } catch (\Exception $e) {
            session()->flash('message', 'Error removing resource assignment: ' . $e->getMessage());
            session()->flash('type', 'error');
            
            return back();
        }
    }

    /**
     * Get available resources for a booking
     */
    public function getAvailableResources(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|in:hotel,vehicle,guide,representative',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $startTime = $request->start_time ? Carbon::parse($request->start_time) : null;
        $endTime = $request->end_time ? Carbon::parse($request->end_time) : null;

        $filters = $request->only(['min_price', 'max_price', 'capacity', 'star_rating', 'language', 'specialization']);

        $resources = $this->resourceAssignmentService->getAvailableResources(
            $request->resource_type,
            $startDate,
            $endDate,
            $startTime,
            $endTime,
            $request->city_id,
            $filters
        );

        $resourceType = $request->resource_type;

        return response()->json([
            'resources' => $resources->map(function ($resource) use ($resourceType) {
                return [
                    'id' => $resource->id,
                    'name' => $resource->name,
                    'description' => $resource->description ?? '',
                    'price' => $this->getResourcePrice($resource, $resourceType),
                    'currency' => $resource->currency ?? 'USD',
                    'city' => $resource->city->name ?? '',
                    'additional_info' => $this->getAdditionalInfo($resource, $resourceType),
                ];
            })
        ]);
    }

    /**
     * Check resource availability
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'resource_type' => 'required|in:hotel,vehicle,guide,representative',
            'resource_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $startTime = $request->start_time ? Carbon::parse($request->start_time) : null;
        $endTime = $request->end_time ? Carbon::parse($request->end_time) : null;

        $isAvailable = $this->resourceAssignmentService->checkAvailability(
            $request->resource_type,
            $request->resource_id,
            $startDate,
            $endDate,
            $startTime,
            $endTime
        );

        return response()->json(['available' => $isAvailable]);
    }

    /**
     * Get resource utilization report
     */
    public function getUtilizationReport(Request $request)
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
     * Get resource price based on type
     */
    private function getResourcePrice($resource, string $resourceType): float
    {
        return match ($resourceType) {
            'hotel' => $resource->price_per_night,
            'vehicle' => $resource->price_per_day,
            'guide' => $resource->price_per_day,
            'representative' => $resource->price_per_day,
            default => 0
        };
    }

    /**
     * Get additional resource information
     */
    private function getAdditionalInfo($resource, string $resourceType): array
    {
        return match ($resourceType) {
            'hotel' => [
                'star_rating' => $resource->star_rating,
                'total_rooms' => $resource->total_rooms,
                'available_rooms' => $resource->available_rooms,
                'utilization' => $resource->utilization_percentage,
            ],
            'vehicle' => [
                'type' => $resource->type,
                'capacity' => $resource->capacity,
                'driver_name' => $resource->driver_name,
                'license_plate' => $resource->license_plate,
            ],
            'guide' => [
                'languages' => $resource->languages,
                'experience_years' => $resource->experience_years,
                'rating' => $resource->average_rating,
                'specializations' => $resource->specializations,
            ],
            'representative' => [
                'languages' => $resource->languages,
                'experience_years' => $resource->experience_years,
                'rating' => $resource->average_rating,
                'company_name' => $resource->company_name,
                'service_areas' => $resource->service_areas,
            ],
            default => []
        };
    }
}




