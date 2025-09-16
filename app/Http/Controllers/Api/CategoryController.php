<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Cache\AppCache;
use Illuminate\Http\Request;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class CategoryController extends Controller
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
        $key = 'categories_'. $request->getQueryString();
//        if (AppCache::has($key)) {
//            $categories = AppCache::get($key, ['data' => []]);
//            $categories['data'] =  CategoryResource::collection(collect($categories['data'])->map(fn($c) => new Category($c)));
//        } else {
            $queryBuilder = new QueryBuilder(new Category, $request);
            $categories = $queryBuilder->build()->paginate();
//            AppCache::put($key, $categories->toArray());
            $collection = CategoryResource::collection($categories->getCollection());
            $categories->setCollection(collect($collection));
//        }
        return $this->send($categories);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param mixed $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug)
    {
         $request->merge([
            'slug' => $slug
        ]);
        $queryBuilder = new QueryBuilder(new Category, $request);
        $category = $queryBuilder->build()->firstOrFail();
        return $this->send(new CategoryResource($category));
    }
}
