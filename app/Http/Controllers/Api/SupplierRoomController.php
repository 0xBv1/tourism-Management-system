<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\SupplierRoomResource;
use App\Models\SupplierRoom;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class SupplierRoomController extends Controller
{
    use HasApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function index(Request $request)
    {
        $queryBuilder = new QueryBuilder(new SupplierRoom, $request);
        $rooms = $queryBuilder->build()->paginate();
        $collection = SupplierRoomResource::collection($rooms->getCollection());
        $rooms->setCollection(collect($collection));
        return $this->send($rooms);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $room = SupplierRoom::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new SupplierRoomResource($room));
    }

    /**
     * Get rooms by hotel.
     *
     * @param  int  $hotelId
     * @return JsonResponse
     */
    public function byHotel(int $hotelId)
    {
        $rooms = SupplierRoom::where('supplier_hotel_id', $hotelId)
            ->where('enabled', true)
            ->where('approved', true)
            ->get();
        
        return $this->send(SupplierRoomResource::collection($rooms));
    }
}
