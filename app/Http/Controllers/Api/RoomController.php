<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\CombinedRoomResource;
use App\Models\SupplierRoom;
use App\Services\SupplierServicesService;
use App\Models\Room;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class RoomController extends Controller
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
        // Regular rooms (legacy rooms tied to regular hotels)
        $queryBuilder = new QueryBuilder(new Room, $request);
        $regularRooms = $queryBuilder->build()->get();

        // Approved supplier rooms only
        $filters = [
            'type' => 'Room',
            'only_approved' => true,
            'only_enabled' => true,
        ];
        // Reuse supplier services service to fetch rooms consistently
        $services = $this->supplierServicesService->getAllServices($filters);
        $supplierRooms = $services->filter(function ($service) {
            return $service->service_type === 'Room';
        })->map(function ($room) {
            if (method_exists($room, 'load')) {
                $room->load('supplierHotel.supplier');
            }
            return $room;
        });

        // Combine
        $allRooms = collect();
        foreach ($regularRooms as $room) {
            $allRooms->push($room);
        }
        foreach ($supplierRooms as $room) {
            $allRooms->push($room);
        }

        // Sort primarily by commission rate (supplier rooms first by high commission)
        $allRooms = $allRooms->sortByDesc(function ($room) {
            if ($room instanceof \App\Models\Room) {
                $commissionRate = 0;
            } else {
                $supplier = $room->supplierHotel ? $room->supplierHotel->supplier : null;
                $commissionRate = $supplier->commission_rate ?? 0;
            }
            $timestamp = $room->created_at ? $room->created_at->timestamp : 0;
            return ($commissionRate * 1000000) + $timestamp;
        });

        // Pagination over combined collection
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginated = $allRooms->slice($offset, $perPage);
        $total = $allRooms->count();

        $data = CombinedRoomResource::collection($paginated);

        $response = [
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int) ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total),
            ]
        ];

        return $this->send($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $room = Room::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new RoomResource($room));
    }
}
