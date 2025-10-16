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
use App\Models\Ticket;
use App\Models\Dahabia;
use App\Models\Restaurant;
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
                'resource_type' => 'required|string|in:hotel,vehicle,guide,representative,extra,ticket,dahabia,restaurant',
                'resource_id' => 'required_if:resource_type,hotel,vehicle,guide,representative,ticket,dahabia,restaurant|integer|min:1',
                'start_date' => 'nullable|date',
                'start_time' => 'nullable|date_format:H:i',
                'end_date' => 'nullable|date',
                'end_time' => 'nullable|date_format:H:i',
                'price_type' => 'nullable|' . $this->getPriceTypeValidation($request->resource_type),
                // hotel fields
                'check_in' => 'nullable|date',
                'check_out' => 'nullable|date',
                'number_of_rooms' => 'nullable|integer|min:0',
                'number_of_adults' => 'nullable|integer|min:0',
                'number_of_children' => 'nullable|integer|min:0',
                'rate_per_adult' => 'nullable|numeric|min:0',
                'rate_per_child' => 'nullable|numeric|min:0',
                // original_price intentionally not accepted from client; computed server-side
                'new_price' => 'nullable|numeric|min:0',
                'increase_percent' => 'nullable|numeric',
                'currency' => 'nullable|string|max:8',
                // restaurant fields
                'selected_meals' => 'nullable|array',
                'selected_meals.*.id' => 'required_with:selected_meals|integer|exists:meals,id',
                'selected_meals.*.name' => 'required_with:selected_meals|string',
                'selected_meals.*.price' => 'required_with:selected_meals|numeric|min:0',
                'selected_meals.*.currency' => 'required_with:selected_meals|string|max:3',
                // guide fields
                'location' => 'nullable|string|max:500',
                'description' => 'nullable|string|max:1000',
                'selected_languages' => 'nullable|array',
                'selected_languages.*' => 'string|in:arabic,english,french,german,spanish,italian,russian,chinese,japanese,korean',
                // internal extra service fields
                'service_name' => 'required_if:resource_type,extra|string|max:255',
                'price' => 'required_if:resource_type,extra|numeric|min:0',
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

            // Validate that the resource exists (skip for internal extra services)
            if ($request->resource_type !== 'extra') {
                $resourceExists = $this->validateResourceExists($request->resource_type, $request->resource_id);
                if (!$resourceExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The selected resource does not exist.'
                    ], 422);
                }
            }

            DB::beginTransaction();

            // Build start/end datetimes first
            $startAt = null;
            $endAt = null;
            if ($request->filled('start_date')) {
                $startAt = $request->start_date . ' ' . ($request->start_time ?? '00:00');
            }
            if ($request->filled('end_date')) {
                $endAt = $request->end_date . ' ' . ($request->end_time ?? '00:00');
            }

            // Check for exact duplicate resources (same resource with same dates/times)
            // Skip duplicate check for internal extra services or when explicitly bypassed
            if ($request->resource_type !== 'extra' && !$request->boolean('bypass_duplicate_check')) {
                $query = InquiryResource::where('inquiry_id', $inquiry_id)
                    ->where('resource_type', $request->resource_type)
                    ->where('resource_id', $request->resource_id);

                // Add date/time checks for more specific duplicate detection
                if ($request->filled('start_date')) {
                    $query->where('start_at', $startAt);
                }
                if ($request->filled('end_date')) {
                    $query->where('end_at', $endAt);
                }
                
                // For hotels, also check check-in/check-out dates
                if ($request->resource_type === 'hotel') {
                    if ($request->filled('check_in')) {
                        $query->where('check_in', $request->check_in);
                    }
                    if ($request->filled('check_out')) {
                        $query->where('check_out', $request->check_out);
                    }
                }

                $existingResource = $query->first();

                if ($existingResource) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This resource with the same dates/times is already added to the inquiry. You can add the same resource with different dates/times.'
                    ], 422);
                }
            }

            // Determine original price from DB for resource type
            $originalPrice = null;
            $currency = $request->currency;
            
            if ($request->resource_type === 'extra') {
                // Internal extra service - use the provided price as original
                $originalPrice = $request->price;
                $currency = $request->currency;
            } elseif ($request->resource_type === 'vehicle') {
                $vehicle = Vehicle::find($request->resource_id);
                if ($vehicle) {
                    if (!$currency) {
                        $currency = $vehicle->currency;
                    }
                    if ($request->price_type === 'hour') {
                        $originalPrice = $vehicle->price_per_hour;
                    } else {
                        $originalPrice = $vehicle->price_per_day;
                    }
                }
            } elseif ($request->resource_type === 'hotel') {
                $hotel = Hotel::find($request->resource_id);
                if ($hotel) {
                    if (!$currency) {
                        $currency = $hotel->currency;
                    }
                    // price per night from model; treat as original unit price
                    $originalPrice = $hotel->price_per_night;
                }
            }

            // Compute effective price
            $effectivePrice = $originalPrice;
            $priceNote = null;
            if ($request->filled('new_price')) {
                $effectivePrice = $request->new_price;
                if ($originalPrice !== null && $originalPrice > 0) {
                    $deltaPercent = round((($effectivePrice - $originalPrice) / $originalPrice) * 100, 2);
                    $priceNote = 'Adjusted from original by ' . $deltaPercent . '%';
                } else {
                    $priceNote = 'Adjusted price set';
                }
            } elseif ($request->filled('increase_percent')) {
                $increase = (float)$request->increase_percent;
                $effectivePrice = $originalPrice !== null ? round($originalPrice * (1 + ($increase / 100)), 2) : null;
                $priceNote = ($increase >= 0 ? 'Increased' : 'Decreased') . ' by ' . $increase . '%';
            } else {
                // If hotel and no adjustments provided, effective price remains original
                $effectivePrice = $originalPrice;
            }

            // Handle restaurant with multiple meals
            if ($request->resource_type === 'restaurant' && $request->has('selected_meals')) {
                $selectedMeals = $request->selected_meals;
                $totalCost = 0;
                $mealDetails = [];
                
                foreach ($selectedMeals as $meal) {
                    $mealCost = $meal['price'];
                    $totalCost += $mealCost;
                    
                    $mealDetails[] = [
                        'meal_id' => $meal['id'],
                        'meal_name' => $meal['name'],
                        'meal_price' => $meal['price'],
                        'meal_currency' => $meal['currency'],
                        'total_cost' => $mealCost,
                        'featured' => $meal['featured'] ?? false
                    ];
                }
                
                // Create inquiry resource with meal details
                $inquiryResource = InquiryResource::create([
                    'inquiry_id' => $inquiry_id,
                    'resource_type' => $request->resource_type,
                    'resource_id' => $request->resource_id,
                    'resource_name' => $this->getResourceName($request->resource_type, $request->resource_id),
                    'added_by' => auth()->id(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'number_of_rooms' => $request->number_of_rooms, // seats
                    'number_of_adults' => $request->number_of_adults,
                    'number_of_children' => $request->number_of_children,
                    'rate_per_adult' => $request->rate_per_adult,
                    'rate_per_child' => $request->rate_per_child,
                    'price_type' => 'meal',
                    'original_price' => $totalCost,
                    'new_price' => null,
                    'increase_percent' => null,
                    'effective_price' => $totalCost,
                    'currency' => $selectedMeals[0]['currency'] ?? 'EGP',
                    'price_note' => 'Multiple meals selected',
                    'resource_details' => json_encode([
                        'selected_meals' => $mealDetails,
                        'total_meals' => count($selectedMeals)
                    ]),
                ]);
            } elseif ($request->resource_type === 'guide' && $request->has('selected_languages')) {
                $selectedLanguages = $request->selected_languages;
                $guidePrice = $request->new_price ?? 0;
                
                // Create inquiry resource with guide details
                $inquiryResource = InquiryResource::create([
                    'inquiry_id' => $inquiry_id,
                    'resource_type' => $request->resource_type,
                    'resource_id' => $request->resource_id,
                    'resource_name' => $this->getResourceName($request->resource_type, $request->resource_id),
                    'added_by' => auth()->id(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'number_of_rooms' => $request->number_of_rooms,
                    'number_of_adults' => $request->number_of_adults,
                    'number_of_children' => $request->number_of_children,
                    'rate_per_adult' => $request->rate_per_adult,
                    'rate_per_child' => $request->rate_per_child,
                    'price_type' => $request->price_type,
                    'original_price' => $guidePrice,
                    'new_price' => $request->new_price,
                    'increase_percent' => $request->increase_percent,
                    'effective_price' => $guidePrice,
                    'currency' => $request->currency ?? 'EGP',
                    'price_note' => 'Guide service',
                    'resource_details' => json_encode([
                        'location' => $request->location,
                        'description' => $request->description,
                        'selected_languages' => $selectedLanguages,
                        'total_languages' => count($selectedLanguages)
                    ]),
                ]);
            } elseif ($request->resource_type === 'representative' && $request->has('selected_languages')) {
                $selectedLanguages = $request->selected_languages;
                $representativePrice = $request->new_price ?? 0;
                
                // Create inquiry resource with representative details
                $inquiryResource = InquiryResource::create([
                    'inquiry_id' => $inquiry_id,
                    'resource_type' => $request->resource_type,
                    'resource_id' => $request->resource_id,
                    'resource_name' => $this->getResourceName($request->resource_type, $request->resource_id),
                    'added_by' => auth()->id(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'number_of_rooms' => $request->number_of_rooms,
                    'number_of_adults' => $request->number_of_adults,
                    'number_of_children' => $request->number_of_children,
                    'rate_per_adult' => $request->rate_per_adult,
                    'rate_per_child' => $request->rate_per_child,
                    'price_type' => $request->price_type,
                    'original_price' => $representativePrice,
                    'new_price' => $request->new_price,
                    'increase_percent' => $request->increase_percent,
                    'effective_price' => $representativePrice,
                    'currency' => $request->currency ?? 'EGP',
                    'price_note' => 'Representative service',
                    'resource_details' => json_encode([
                        'location' => $request->location,
                        'description' => $request->description,
                        'selected_languages' => $selectedLanguages,
                        'total_languages' => count($selectedLanguages)
                    ]),
                ]);
            } elseif ($request->resource_type === 'extra') {
                // Create internal extra service
                $inquiryResource = InquiryResource::create([
                    'inquiry_id' => $inquiry_id,
                    'resource_type' => $request->resource_type,
                    'resource_id' => null, // No external resource ID for internal services
                    'added_by' => auth()->id(),
                    'resource_name' => $request->service_name,
                    'price_type' => $request->price_type,
                    'original_price' => $originalPrice,
                    'effective_price' => $effectivePrice,
                    'currency' => $currency,
                    'price_note' => 'Internal extra service',
                    'resource_details' => json_encode([
                        'service_name' => $request->service_name,
                        'description' => $request->description,
                        'price_type' => $request->price_type,
                        'is_internal' => true
                    ]),
                ]);
            } else {
                // Get the resource name for external resources
                $resourceName = $this->getResourceName($request->resource_type, $request->resource_id);
                
                // Create the inquiry resource for other types
                $inquiryResource = InquiryResource::create([
                    'inquiry_id' => $inquiry_id,
                    'resource_type' => $request->resource_type,
                    'resource_id' => $request->resource_id,
                    'resource_name' => $resourceName,
                    'added_by' => auth()->id(),
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'number_of_rooms' => $request->number_of_rooms,
                    'number_of_adults' => $request->number_of_adults,
                    'number_of_children' => $request->number_of_children,
                    'rate_per_adult' => $request->rate_per_adult,
                    'rate_per_child' => $request->rate_per_child,
                    'price_type' => $request->price_type,
                    'original_price' => $originalPrice,
                    'new_price' => $request->new_price,
                    'increase_percent' => $request->increase_percent,
                    'effective_price' => $effectivePrice,
                    'currency' => $currency,
                    'price_note' => $priceNote,
                ]);
            }

            // Log the activity
            $inquiryResource->logActivity('add_resource', [
                'inquiry_id' => $inquiry_id,
                'resource_type' => $request->resource_type,
                'resource_id' => $request->resource_id ?? 'internal',
                'resource_name' => $request->resource_type === 'extra' ? $request->service_name : null,
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
                    'resource_name' => $inquiryResource->resource_name ?? $inquiryResource->getResourceNameAttribute(),
                    'added_by' => $inquiryResource->addedBy?->name ?? 'Unknown User',
                    'created_at' => $inquiryResource->created_at->format('Y-m-d H:i:s'),
                    'start_at' => optional($inquiryResource->start_at)->format('Y-m-d H:i'),
                    'end_at' => optional($inquiryResource->end_at)->format('Y-m-d H:i'),
                    'price_type' => $inquiryResource->price_type,
                    'currency' => $inquiryResource->currency,
                    'original_price' => $inquiryResource->original_price,
                    'new_price' => $inquiryResource->new_price,
                    'increase_percent' => $inquiryResource->increase_percent,
                    'effective_price' => $inquiryResource->effective_price,
                    'price_note' => $inquiryResource->price_note,
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
            'resource_type' => 'required|string|in:hotel,vehicle,guide,representative,extra,ticket,dahabia,restaurant',
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
     * Get detailed information about a specific inquiry resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $inquiryResource = InquiryResource::with(['resource', 'addedBy', 'inquiry'])->findOrFail($id);

            // Check if user can view this resource
            if (!Gate::allows('manage-inquiry-resources') && !Gate::allows('view', $inquiryResource->inquiry)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to view this resource.'
                ], 403);
            }

            $resourceDetails = $inquiryResource->resource_details;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $inquiryResource->id,
                    'inquiry_id' => $inquiryResource->inquiry_id,
                    'resource_type' => $inquiryResource->resource_type,
                    'resource_id' => $inquiryResource->resource_id,
                    'resource_details' => $resourceDetails,
                    'start_at' => $inquiryResource->start_at?->format('Y-m-d H:i'),
                    'end_at' => $inquiryResource->end_at?->format('Y-m-d H:i'),
                    'check_in' => $inquiryResource->check_in?->format('Y-m-d'),
                    'check_out' => $inquiryResource->check_out?->format('Y-m-d'),
                    'number_of_rooms' => $inquiryResource->number_of_rooms,
                    'number_of_adults' => $inquiryResource->number_of_adults,
                    'number_of_children' => $inquiryResource->number_of_children,
                    'rate_per_adult' => $inquiryResource->rate_per_adult,
                    'rate_per_child' => $inquiryResource->rate_per_child,
                    'price_type' => $inquiryResource->price_type,
                    'original_price' => $inquiryResource->original_price,
                    'new_price' => $inquiryResource->new_price,
                    'increase_percent' => $inquiryResource->increase_percent,
                    'effective_price' => $inquiryResource->effective_price,
                    'currency' => $inquiryResource->currency,
                    'price_note' => $inquiryResource->price_note,
                    'formatted_price' => $inquiryResource->formatted_price,
                    'duration_in_days' => $inquiryResource->duration_in_days,
                    'total_cost' => $inquiryResource->total_cost,
                    'added_by' => [
                        'id' => $inquiryResource->addedBy->id,
                        'name' => $inquiryResource->addedBy->name,
                        'roles' => $inquiryResource->addedBy->roles->pluck('name')->toArray(),
                    ],
                    'created_at' => $inquiryResource->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $inquiryResource->updated_at->format('Y-m-d H:i:s'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Resource not found or error occurred.',
                'error' => $e->getMessage()
            ], 404);
        }
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
            'ticket' => Ticket::where('id', $resourceId)->exists(),
            'dahabia' => Dahabia::where('id', $resourceId)->exists(),
            'restaurant' => Restaurant::where('id', $resourceId)->exists(),
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
            'ticket' => Ticket::active()->with('city')->get(['id', 'name', 'city_id', 'price_per_person', 'currency'])->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'name' => $ticket->name,
                    'city' => $ticket->city->name ?? 'Unknown City',
                    'price_per_person' => $ticket->price_per_person,
                    'currency' => $ticket->currency
                ];
            })->toArray(),
            'dahabia' => Dahabia::active()->with('city')->get(['id', 'name', 'city_id', 'price_per_person', 'price_per_charter', 'currency'])->map(function($dahabia) {
                return [
                    'id' => $dahabia->id,
                    'name' => $dahabia->name,
                    'city' => $dahabia->city->name ?? 'Unknown City',
                    'price_per_person' => $dahabia->price_per_person,
                    'price_per_charter' => $dahabia->price_per_charter,
                    'currency' => $dahabia->currency
                ];
            })->toArray(),
            'restaurant' => Restaurant::active()->with('city')->get(['id', 'name', 'city_id', 'currency'])->map(function($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'city' => $restaurant->city->name ?? 'Unknown City',
                    'currency' => $restaurant->currency
                ];
            })->toArray(),
            default => []
        };
    }

    /**
     * Get price type validation rules based on resource type.
     */
    private function getPriceTypeValidation($resourceType): string
    {
        return match($resourceType) {
            'hotel' => 'in:day',
            'vehicle' => 'in:day,hour',
            'guide', 'representative' => 'in:hour,day,half_day',
            'extra' => 'in:per_person,per_group,per_hour,per_day,fixed',
            'ticket' => 'in:person',
            'dahabia' => 'in:person,charter',
            'restaurant' => 'in:meal',
            default => 'in:day,hour'
        };
    }

    /**
     * Get the resource name for a given resource type and ID.
     */
    private function getResourceName(string $resourceType, int $resourceId): string
    {
        return match($resourceType) {
            'hotel' => Hotel::find($resourceId)?->name ?? 'Unknown Hotel',
            'vehicle' => Vehicle::find($resourceId)?->name ?? 'Unknown Vehicle',
            'guide' => Guide::find($resourceId)?->name ?? 'Unknown Guide',
            'representative' => Representative::find($resourceId)?->name ?? 'Unknown Representative',
            'extra' => Extra::find($resourceId)?->name ?? 'Unknown Extra Service',
            'ticket' => Ticket::find($resourceId)?->name ?? 'Unknown Ticket',
            'dahabia' => Dahabia::find($resourceId)?->name ?? 'Unknown Dahabia',
            'restaurant' => Restaurant::find($resourceId)?->name ?? 'Unknown Restaurant',
            default => 'Unknown Resource'
        };
    }
}
