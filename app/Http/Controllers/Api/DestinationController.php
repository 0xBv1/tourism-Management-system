<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Cache\AppCache;
use Illuminate\Http\Request;
use App\Http\Resources\Api\DestinationResource;
use App\Models\Destination;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class DestinationController extends Controller
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
        $key = 'destinations_'. $request->getQueryString();
//        if (AppCache::has($key)) {
//            $destinations = AppCache::get($key, ['data' => []]);
//            $destinations['data'] = DestinationResource::collection(collect($destinations['data'])->map(fn($d) => new Destination($d)));
//        } else {
            $queryBuilder = new QueryBuilder(new Destination, $request);
            $destinations = $queryBuilder->build()->paginate();
//            AppCache::put($key, $destinations->toArray());
            $collection = DestinationResource::collection($destinations->getCollection());
            $destinations->setCollection(collect($collection));
//        }
        return $this->send($destinations);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug)
    {
        $request->merge([
            'slug' => $slug
        ]);
        $queryBuilder = new QueryBuilder(new Destination, $request);
        $destination = $queryBuilder->build()->firstOrFail();
        return $this->send(new DestinationResource($destination));
    }
}
