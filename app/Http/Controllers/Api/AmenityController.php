<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\AmenityResource;
use App\Models\Amenity;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class AmenityController extends Controller
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
        $queryBuilder = new QueryBuilder(new Amenity, $request);
        $amenities = $queryBuilder->build()->paginate();
        $collection = AmenityResource::collection($amenities->getCollection());
        $amenities->setCollection(collect($collection));
        return $this->send($amenities);
    }

    /**
     * Display the specified resource.
     *
     * @param  mixed  $id
     * @return JsonResponse
     */
    public function show(mixed $id)
    {
        $amenity = Amenity::findOrFail($id);
        return $this->send(new AmenityResource($amenity));
    }
}
