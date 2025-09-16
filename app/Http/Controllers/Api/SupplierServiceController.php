<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SupplierServiceResource;
use App\Models\SupplierHotel;
use App\Models\SupplierTour;
use App\Models\SupplierTrip;
use App\Models\SupplierTransport;
use App\Services\SupplierServicesService;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SupplierServiceController extends Controller
{
    use HasApiResponse;

    protected SupplierServicesService $supplierServicesService;

    public function __construct(SupplierServicesService $supplierServicesService)
    {
        $this->supplierServicesService = $supplierServicesService;
    }

    /**
     * Display a listing of all supplier services.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'query' => $request->get('query'),
            'status' => $request->get('status'),
            'type' => $request->get('type'),
            'supplier' => $request->get('supplier'),
            'city' => $request->get('city'),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'sort_by' => $request->get('sort_by', 'created_at'),
            'sort_order' => $request->get('sort_order', 'desc'),
            'per_page' => $request->get('per_page', 15),
            'recommended' => $request->get('recommended', false),
        ];

        // Get services based on filters
        $services = $this->getServices($filters);

        // Apply pagination
        $perPage = (int) $filters['per_page'];
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedServices = $services->slice($offset, $perPage);
        $total = $services->count();

        // Transform to resources
        $resources = SupplierServiceResource::collection($paginatedServices);

        return $this->send([
            'data' => $resources,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified supplier service.
     *
     * @param string $type
     * @param mixed $id
     * @return JsonResponse
     */
    public function show(string $type, mixed $id): JsonResponse
    {
        $service = $this->getServiceById($type, $id);

        if (!$service) {
            return $this->send(null, 'Service not found', 404);
        }

        return $this->send(new SupplierServiceResource($service));
    }

    /**
     * Get recommended services based on commission rates.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function recommended(Request $request): JsonResponse
    {
        $filters = [
            'type' => $request->get('type'),
            'city' => $request->get('city'),
            'limit' => $request->get('limit', 10),
        ];

        $services = $this->getRecommendedServices($filters);

        return $this->send([
            'data' => SupplierServiceResource::collection($services),
            'filters' => $filters,
        ]);
    }

    /**
     * Get services by supplier.
     *
     * @param Request $request
     * @param int $supplierId
     * @return JsonResponse
     */
    public function bySupplier(Request $request, int $supplierId): JsonResponse
    {
        $filters = [
            'supplier_id' => $supplierId,
            'type' => $request->get('type'),
            'status' => $request->get('status'),
            'per_page' => $request->get('per_page', 15),
        ];

        $services = $this->getServicesBySupplier($filters);

        // Apply pagination
        $perPage = (int) $filters['per_page'];
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $paginatedServices = $services->slice($offset, $perPage);
        $total = $services->count();

        return $this->send([
            'data' => SupplierServiceResource::collection($paginatedServices),
            'pagination' => [
                'current_page' => (int) $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ],
            'supplier_id' => $supplierId,
        ]);
    }

    /**
     * Get services with filters.
     */
    protected function getServices(array $filters): Collection
    {
        $services = $this->supplierServicesService->getAllServices($filters);

        // Apply additional filters
        if ($filters['city']) {
            $services = $services->filter(function ($service) use ($filters) {
                return stripos($service->city ?? '', $filters['city']) !== false;
            });
        }

        if ($filters['min_price'] || $filters['max_price']) {
            $services = $services->filter(function ($service) use ($filters) {
                $price = $service->service_price ?? 0;
                if ($filters['min_price'] && $price < $filters['min_price']) {
                    return false;
                }
                if ($filters['max_price'] && $price > $filters['max_price']) {
                    return false;
                }
                return true;
            });
        }

        // Apply sorting
        $sortBy = $filters['sort_by'];
        $sortOrder = $filters['sort_order'];

        if ($sortBy === 'commission_rate') {
            $services = $services->sortBy('supplier.commission_rate', SORT_REGULAR, $sortOrder === 'desc');
        } elseif ($sortBy === 'price') {
            $services = $services->sortBy('service_price', SORT_REGULAR, $sortOrder === 'desc');
        } else {
            $services = $services->sortBy($sortBy, SORT_REGULAR, $sortOrder === 'desc');
        }

        return $services;
    }

    /**
     * Get recommended services based on commission rates.
     */
    protected function getRecommendedServices(array $filters): Collection
    {
        $services = $this->supplierServicesService->getAllServices($filters);

        // Filter only approved and enabled services
        $services = $services->filter(function ($service) {
            return $service->approved && $service->enabled;
        });

        // Sort by commission rate (highest first) and then by rating/quality
        $services = $services->sortByDesc(function ($service) {
            $commissionRate = $service->supplier->commission_rate ?? 0;
            $rating = $service->rating ?? 0;
            $stars = $service->stars ?? 0;
            
            // Weighted score: 70% commission rate, 20% rating, 10% stars
            return ($commissionRate * 0.7) + ($rating * 0.2) + ($stars * 0.1);
        });

        return $services->take($filters['limit'] ?? 10);
    }

    /**
     * Get services by supplier.
     */
    protected function getServicesBySupplier(array $filters): Collection
    {
        $services = $this->supplierServicesService->getAllServices($filters);

        // Filter by supplier ID
        $services = $services->filter(function ($service) use ($filters) {
            return $service->supplier_id == $filters['supplier_id'];
        });

        return $services->sortByDesc('created_at');
    }

    /**
     * Get service by type and ID.
     */
    protected function getServiceById(string $type, mixed $id)
    {
        switch (strtolower($type)) {
            case 'hotel':
                return SupplierHotel::with('supplier.user')
                    ->where(function ($query) use ($id) {
                        $query->where('id', $id)->orWhere('slug', $id);
                    })
                    ->first();

            case 'tour':
                return SupplierTour::with('supplier.user')
                    ->where(function ($query) use ($id) {
                        $query->where('id', $id)->orWhere('slug', $id);
                    })
                    ->first();

            case 'trip':
                return SupplierTrip::with('supplier.user')
                    ->where(function ($query) use ($id) {
                        $query->where('id', $id)->orWhere('slug', $id);
                    })
                    ->first();

            case 'transport':
                return SupplierTransport::with('supplier.user')
                    ->where(function ($query) use ($id) {
                        $query->where('id', $id)->orWhere('slug', $id);
                    })
                    ->first();

            default:
                return null;
        }
    }
}
