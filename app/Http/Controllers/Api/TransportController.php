<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transport;
use App\Models\SupplierTransport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransportController extends Controller
{
    /**
     * Display a listing of transports (both regular and supplier).
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Transport model uses 'enabled' column, SupplierTransport uses 'approved' column
            $query = Transport::query()->where('enabled', true);
            $supplierQuery = SupplierTransport::query()->where('approved', true);

            // Apply search filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('origin_location', 'like', "%{$search}%")
                      ->orWhere('destination_location', 'like', "%{$search}%")
                      ->orWhere('contact_notes', 'like', "%{$search}%");
                });
                $supplierQuery->where(function ($q) use ($search) {
                    $q->where('origin_location', 'like', "%{$search}%")
                      ->orWhere('destination_location', 'like', "%{$search}%")
                      ->orWhere('contact_notes', 'like', "%{$search}%");
                });
            }

            // Apply type filters (Transport uses 'transport_type', SupplierTransport uses 'vehicle_type')
            if ($request->filled('type')) {
                $query->where('transport_type', $request->type);
                $supplierQuery->where('vehicle_type', $request->type);
            }

            // Apply vehicle type filters
            if ($request->filled('vehicle_type')) {
                $query->where('vehicle_type', $request->vehicle_type);
                $supplierQuery->where('vehicle_type', $request->vehicle_type);
            }

            // Apply route type filters
            if ($request->filled('route_type')) {
                $query->where('route_type', $request->route_type);
                $supplierQuery->where('route_type', $request->route_type);
            }

            // Apply destination filters (using city IDs for supplier transports)
            if ($request->filled('destination_id')) {
                $query->where('destination_location', 'like', '%' . $request->destination_id . '%');
                $supplierQuery->where('destination_city_id', $request->destination_id);
            }

            // Apply price range filters
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
                $supplierQuery->where('price', '>=', $request->min_price);
            }
            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
                $supplierQuery->where('price', '<=', $request->max_price);
            }

            // Apply sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $query->orderBy($sortBy, $sortOrder);
            $supplierQuery->orderBy($sortBy, $sortOrder);

            // Get paginated results
            $perPage = $request->get('per_page', 15);
            
            $transports = $query->paginate($perPage);
            $supplierTransports = $supplierQuery->paginate($perPage);

            // Combine and format results
            $combinedResults = [];
            
            // Add regular transports
            foreach ($transports->items() as $transport) {
                $combinedResults[] = [
                    'id' => $transport->id,
                    'name' => $transport->origin_location . ' to ' . $transport->destination_location,
                    'type' => $transport->transport_type,
                    'vehicle_type' => $transport->vehicle_type,
                    'route_type' => $transport->route_type,
                    'description' => $transport->contact_notes,
                    'price' => $transport->price,
                    'currency' => $transport->currency,
                    'featured_image' => $transport->featured_image ? asset('storage/' . $transport->featured_image) : null,
                    'images' => $transport->images ? array_map(function($image) {
                        return asset('storage/' . $image);
                    }, $transport->images) : [],
                    'amenities' => $transport->amenities,
                    'is_supplier' => false,
                    'supplier_id' => null,
                    'origin_location' => $transport->origin_location,
                    'destination_location' => $transport->destination_location,
                    'departure_time' => $transport->departure_time,
                    'arrival_time' => $transport->arrival_time,
                    'estimated_travel_time' => $transport->estimated_travel_time,
                    'distance' => $transport->distance,
                    'seating_capacity' => $transport->seating_capacity,
                    'created_at' => $transport->created_at,
                    'updated_at' => $transport->updated_at,
                ];
            }

            // Add supplier transports
            foreach ($supplierTransports->items() as $transport) {
                $combinedResults[] = [
                    'id' => $transport->id,
                    'name' => $transport->origin_location . ' to ' . $transport->destination_location,
                    'type' => $transport->vehicle_type, // Using vehicle_type as type for supplier transports
                    'vehicle_type' => $transport->vehicle_type,
                    'route_type' => $transport->route_type,
                    'description' => $transport->contact_notes,
                    'price' => $transport->price,
                    'currency' => $transport->currency,
                    'featured_image' => $transport->featured_image ? asset('storage/' . $transport->featured_image) : null,
                    'images' => $transport->images ? array_map(function($image) {
                        return asset('storage/' . $image);
                    }, $transport->images) : [],
                    'amenities' => $transport->amenities,
                    'is_supplier' => true,
                    'supplier_id' => $transport->supplier_id,
                    'supplier_name' => $transport->supplier->company_name ?? 'Unknown Supplier',
                    'origin_location' => $transport->origin_location,
                    'destination_location' => $transport->destination_location,
                    'departure_time' => $transport->departure_time,
                    'arrival_time' => $transport->arrival_time,
                    'estimated_travel_time' => $transport->estimated_travel_time,
                    'distance' => $transport->distance,
                    'seating_capacity' => $transport->seating_capacity,
                    'created_at' => $transport->created_at,
                    'updated_at' => $transport->updated_at,
                ];
            }

            // Sort combined results by the specified field
            usort($combinedResults, function($a, $b) use ($sortBy, $sortOrder) {
                $aValue = $a[$sortBy] ?? '';
                $bValue = $b[$sortBy] ?? '';
                
                if ($sortOrder === 'asc') {
                    return $aValue <=> $bValue;
                } else {
                    return $bValue <=> $aValue;
                }
            });

            // Manual pagination for combined results
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $paginatedResults = array_slice($combinedResults, $offset, $perPage);

            $response = [
                'data' => $paginatedResults,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page' => (int) $perPage,
                    'total' => count($combinedResults),
                    'last_page' => ceil(count($combinedResults) / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, count($combinedResults)),
                ],
                'filters' => [
                    'search' => $request->search,
                    'type' => $request->type,
                    'vehicle_type' => $request->vehicle_type,
                    'route_type' => $request->route_type,
                    'destination_id' => $request->destination_id,
                    'min_price' => $request->min_price,
                    'max_price' => $request->max_price,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder,
                ],
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch transports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified transport.
     */
    public function show($id): JsonResponse
    {
        try {
            // First try to find in regular transports (using 'enabled')
            $transport = Transport::where('enabled', true)->find($id);
            $isSupplier = false;

            // If not found, try supplier transports (using 'approved')
            if (!$transport) {
                $transport = SupplierTransport::where('approved', true)->with('supplier.user')->find($id);
                $isSupplier = true;
            }

            if (!$transport) {
                return response()->json([
                    'message' => 'Transport not found'
                ], 404);
            }

            $response = [
                'id' => $transport->id,
                'name' => $transport->origin_location . ' to ' . $transport->destination_location,
                'type' => $isSupplier ? $transport->vehicle_type : $transport->transport_type,
                'vehicle_type' => $transport->vehicle_type,
                'route_type' => $transport->route_type,
                'description' => $transport->contact_notes,
                'price' => $transport->price,
                'currency' => $transport->currency,
                'featured_image' => $transport->featured_image ? asset('storage/' . $transport->featured_image) : null,
                'images' => $transport->images ? array_map(function($image) {
                    return asset('storage/' . $image);
                }, $transport->images) : [],
                'amenities' => $transport->amenities,
                'is_supplier' => $isSupplier,
                'origin_location' => $transport->origin_location,
                'destination_location' => $transport->destination_location,
                'departure_time' => $transport->departure_time,
                'arrival_time' => $transport->arrival_time,
                'estimated_travel_time' => $transport->estimated_travel_time,
                'distance' => $transport->distance,
                'seating_capacity' => $transport->seating_capacity,
                'created_at' => $transport->created_at,
                'updated_at' => $transport->updated_at,
            ];

            if ($isSupplier && $transport->supplier) {
                $response['supplier'] = [
                    'id' => $transport->supplier->id,
                    'company_name' => $transport->supplier->company_name,
                    'contact_person' => $transport->supplier->user->name ?? 'N/A',
                    'email' => $transport->supplier->user->email ?? 'N/A',
                    'phone' => $transport->supplier->phone ?? 'N/A',
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch transport',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transport statistics.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total' => Transport::where('enabled', true)->count() + SupplierTransport::where('approved', true)->count(),
                'regular' => Transport::where('enabled', true)->count(),
                'supplier' => SupplierTransport::where('approved', true)->count(),
                'types' => [
                    'bus' => Transport::where('enabled', true)->where('transport_type', 'bus')->count() + 
                             SupplierTransport::where('approved', true)->where('vehicle_type', 'bus')->count(),
                    'train' => Transport::where('enabled', true)->where('transport_type', 'train')->count() + 
                               SupplierTransport::where('approved', true)->where('vehicle_type', 'train')->count(),
                    'ferry' => Transport::where('enabled', true)->where('transport_type', 'ferry')->count() + 
                               SupplierTransport::where('approved', true)->where('vehicle_type', 'ferry')->count(),
                    'plane' => Transport::where('enabled', true)->where('transport_type', 'plane')->count() + 
                               SupplierTransport::where('approved', true)->where('vehicle_type', 'plane')->count(),
                    'car' => Transport::where('enabled', true)->where('transport_type', 'car')->count() + 
                             SupplierTransport::where('approved', true)->where('vehicle_type', 'car')->count(),
                    'van' => Transport::where('enabled', true)->where('transport_type', 'van')->count() + 
                             SupplierTransport::where('approved', true)->where('vehicle_type', 'van')->count(),
                    'boat' => Transport::where('enabled', true)->where('transport_type', 'boat')->count() + 
                              SupplierTransport::where('approved', true)->where('vehicle_type', 'boat')->count(),
                    'helicopter' => Transport::where('enabled', true)->where('transport_type', 'helicopter')->count() + 
                                   SupplierTransport::where('approved', true)->where('vehicle_type', 'helicopter')->count(),
                ],
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch transport statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
