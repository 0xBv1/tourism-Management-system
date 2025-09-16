<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\HotelResource;
use App\Http\Resources\Api\CombinedHotelResource;
use App\Models\Hotel;
use App\Models\SupplierHotel;
use App\Services\Query\QueryBuilder;
use App\Services\SupplierServicesService;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    use HasApiResponse;

    protected SupplierServicesService $supplierServicesService;

    public function __construct(SupplierServicesService $supplierServicesService)
    {
        $this->supplierServicesService = $supplierServicesService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        // Get regular hotels
        $queryBuilder = new QueryBuilder(new Hotel, $request);
        $regularHotels = $queryBuilder->build()->get();
        
        // Get approved supplier hotels
        $supplierHotelsFilters = [
            'type' => 'Hotel',
            'only_approved' => true,
            'only_enabled' => true,
        ];
        
        $supplierHotels = $this->getApprovedSupplierHotels($supplierHotelsFilters, $request);
        
        // Combine both collections into a single array
        $allHotels = collect();
        
        // Add regular hotels
        foreach ($regularHotels as $hotel) {
            $allHotels->push($hotel);
        }
        
        // Add supplier hotels
        foreach ($supplierHotels['data'] as $hotel) {
            $allHotels->push($hotel);
        }
        
        // Sort by commission rate (highest first) and then by creation date (newest first)
        $allHotels = $allHotels->sortByDesc(function ($hotel) {
            // For regular hotels, commission rate is 0
            if ($hotel instanceof \App\Models\Hotel) {
                $commissionRate = 0;
            } else {
                // For supplier hotels, use commission rate
                $commissionRate = $hotel->supplier->commission_rate ?? 0;
            }
            
            // Create a composite key: commission rate * 1000000 + timestamp
            // This ensures commission rate is the primary sort, then creation date
            $timestamp = $hotel->created_at ? $hotel->created_at->timestamp : 0;
            return ($commissionRate * 1000000) + $timestamp;
        });
        
        // Apply pagination to combined collection
        $perPage = (int) $request->get('per_page', 15);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedHotels = $allHotels->slice($offset, $perPage);
        $total = $allHotels->count();
        
        // Transform using CombinedHotelResource
        $hotelsCollection = CombinedHotelResource::collection($paginatedHotels);
        
        $response = [
            'data' => $hotelsCollection,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ]
        ];
        
        return $this->send($response);
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $hotel = Hotel::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new HotelResource($hotel));
    }

    /**
     * Get approved supplier hotels
     */
    protected function getApprovedSupplierHotels(array $filters, Request $request): array
    {
        // Get all approved supplier hotels
        $services = $this->supplierServicesService->getAllServices($filters);
        
        // Filter only hotels
        $hotels = $services->filter(function ($service) {
            return $service->service_type === 'Hotel';
        });

        return [
            'data' => $hotels,
            'pagination' => [
                'current_page' => 1,
                'per_page' => $hotels->count(),
                'total' => $hotels->count(),
                'last_page' => 1,
                'from' => 1,
                'to' => $hotels->count(),
            ],
        ];
    }
}
