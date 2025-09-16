<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Api\BlogResource;
use App\Models\Blog;
use App\Traits\Response\HasApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\Query\QueryBuilder;
use Exception;

class BlogController extends Controller
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
        $queryBuilder = new QueryBuilder(new Blog, $request);
        $blogs = $queryBuilder->build()->paginate();
        $collection = BlogResource::collection($blogs->getCollection());
        $blogs->setCollection(collect($collection));
        return $this->send($blogs);
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
        $queryBuilder = new QueryBuilder(new Blog, $request);
        $category = $queryBuilder->build()->firstOrFail();
        return $this->send(new BlogResource($category));
    }
}
