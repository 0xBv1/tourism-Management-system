<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\SupplierHotelResource;
use App\Models\SupplierHotel;
use App\Services\Query\QueryBuilder;
use App\Traits\Response\HasApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierHotelController extends Controller
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
        $queryBuilder = new QueryBuilder(new SupplierHotel, $request);
        $hotels = $queryBuilder->build()->paginate();
        $collection = SupplierHotelResource::collection($hotels->getCollection());
        $hotels->setCollection(collect($collection));
        return $this->send($hotels);
    }

    /**
     * Display the specified resource.
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $hotel = SupplierHotel::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->firstOrFail();
        return $this->send(new SupplierHotelResource($hotel));
    }
}



