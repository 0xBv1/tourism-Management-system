<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\InquiryResource;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\Extra;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InquiryResourceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $inquiry_id): JsonResponse
    {
        try {
            // Check authorization
            if (!Gate::allows('manage-inquiry-resources')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to manage inquiry resources. Only Operation, Admin, and Administrator roles can add resources.'
                ], 403);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'resource_type' => 'required|string|in:hotel,vehicle,guide,representative,extra',
                'resource_id' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if inquiry exists
            $inquiry = Inquiry::findOrFail($inquiry_id);

            // Validate that the resource exists
            $resourceExists = $this->validateResourceExists($request->resource_type, $request->resource_id);
            if (!$resourceExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'The selected resource does not exist.'
                ], 422);
            }

            DB::beginTransaction();

            // Check if resource is already added to this inquiry
            $existingResource = InquiryResource::where('inquiry_id', $inquiry_id)
                ->where('resource_type', $request->resource_type)
                ->where('resource_id', $request->resource_id)
                ->first();

            if ($existingResource) {
                return response()->json([
                    'success' => false,
                    'message' => 'This resource is already added to the inquiry.'
                ], 422);
            }

            // Create the inquiry resource
            $inquiryResource = InquiryResource::create([
                'inquiry_id' => $inquiry_id,
                'resource_type' => $request->resource_type,
                'resource_id' => $request->resource_id,
                'added_by' => auth()->id(),
            ]);

            // Log the activity
            $inquiryResource->logActivity('add_resource', [
                'inquiry_id' => $inquiry_id,
                'resource_type' => $request->resource_type,
                'resource_id' => $request->resource_id,
            ]);

            DB::commit();

            // Load the resource relationship for response
            $inquiryResource->load('resource', 'addedBy');

            return response()->json([
                'success' => true,
                'message' => 'Resource added successfully.',
                'data' => [
                    'id' => $inquiryResource->id,
                    'resource_type' => $inquiryResource->resource_type,
                    'resource_name' => $inquiryResource->resource_name,
                    'added_by' => $inquiryResource->addedBy->name,
                    'created_at' => $inquiryResource->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add resource. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        // Check authorization
        if (!Gate::allows('manage-inquiry-resources')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to manage inquiry resources.'
            ], 403);
        }

        try {
            $inquiryResource = InquiryResource::findOrFail($id);

            // Store data for logging before deletion
            $logData = [
                'inquiry_id' => $inquiryResource->inquiry_id,
                'resource_type' => $inquiryResource->resource_type,
                'resource_id' => $inquiryResource->resource_id,
            ];

            // Log the activity before deletion
            $inquiryResource->logActivity('remove_resource', $logData);

            $inquiryResource->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resource removed successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove resource. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available resources for a specific type.
     */
    public function getAvailableResources(Request $request): JsonResponse
    {
        // Check authorization
        if (!Gate::allows('manage-inquiry-resources')) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to manage inquiry resources.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'resource_type' => 'required|string|in:hotel,vehicle,guide,representative,extra',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $resources = $this->getResourcesByType($request->resource_type);

        return response()->json([
            'success' => true,
            'data' => $resources
        ]);
    }

    /**
     * Validate that a resource exists in the database.
     */
    private function validateResourceExists(string $resourceType, int $resourceId): bool
    {
        return match($resourceType) {
            'hotel' => Hotel::where('id', $resourceId)->exists(),
            'vehicle' => Vehicle::where('id', $resourceId)->exists(),
            'guide' => Guide::where('id', $resourceId)->exists(),
            'representative' => Representative::where('id', $resourceId)->exists(),
            'extra' => Extra::where('id', $resourceId)->exists(),
            default => false
        };
    }

    /**
     * Get resources by type for dropdown/autocomplete.
     */
    private function getResourcesByType(string $resourceType): array
    {
        return match($resourceType) {
            'hotel' => Hotel::active()->with('city')->get(['id', 'name', 'city_id'])->map(function($hotel) {
                return [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'city' => $hotel->city->name ?? 'Unknown City'
                ];
            })->toArray(),
            'vehicle' => Vehicle::active()->with('city')->get(['id', 'name', 'type', 'city_id', 'price_per_day', 'price_per_hour', 'currency'])->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                    'type' => $vehicle->type,
                    'city' => $vehicle->city->name ?? 'Unknown City',
                    'price_per_day' => $vehicle->price_per_day,
                    'price_per_hour' => $vehicle->price_per_hour,
                    'currency' => $vehicle->currency
                ];
            })->toArray(),
            'guide' => Guide::active()->with('city')->get(['id', 'name', 'city_id'])->map(function($guide) {
                return [
                    'id' => $guide->id,
                    'name' => $guide->name,
                    'city' => $guide->city->name ?? 'Unknown City'
                ];
            })->toArray(),
            'representative' => Representative::active()->with('city')->get(['id', 'name', 'city_id'])->map(function($rep) {
                return [
                    'id' => $rep->id,
                    'name' => $rep->name,
                    'city' => $rep->city->name ?? 'Unknown City'
                ];
            })->toArray(),
            'extra' => Extra::active()->get(['id', 'name', 'category', 'price', 'currency'])->map(function($extra) {
                return [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'category' => $extra->category,
                    'price' => $extra->formatted_price
                ];
            })->toArray(),
            default => []
        };
    }
}
