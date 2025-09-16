<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\ResourceCreatedEvent;
use App\Models\BlogCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BlogCategoryRequest;
use App\DataTables\BlogCategoryDataTable;

class BlogCategoryController extends Controller
{

    public function index(BlogCategoryDataTable $dataTable)
    {
        return $dataTable->render('dashboard.blog-categories.index');
    }


    public function create()
    {
        $categories = BlogCategory::orderByTranslation('title')->get()->mapWithKeys(function($category) {
            return [$category->id => $category->translate('en')->title];
        })->toArray();
        return view('dashboard.blog-categories.create', compact('categories'));
    }


    public function store(BlogCategoryRequest $request)
    {
        // Create blog category with non-translatable fields
        $blogCategory = BlogCategory::create($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $blogCategory->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $blogCategory->save();
        
        // Handle relationships
        $blogCategory->seo()->create($request->get('seo'));
        $blogCategory->relatedTours()->sync($request->get('related_tours'));
        session()->flash('message', 'Blog Category Created Successfully!');
        session()->flash('type', 'success');
        ResourceCreatedEvent::dispatch($blogCategory);
        return redirect()->route('dashboard.blog-categories.edit', $blogCategory);
    }


    public function show(BlogCategory $blogCategory)
    {
        //
    }


    public function edit(BlogCategory $blogCategory)
    {
        $categories = BlogCategory::where('blog_categories.id', '!=', $blogCategory->id)
            ->orderByTranslation('title')
            ->get()
            ->mapWithKeys(function($category) {
                return [$category->id => $category->translate('en')->title];
            })->toArray();
        return view('dashboard.blog-categories.edit', compact('blogCategory', 'categories'));
    }


    public function update(BlogCategoryRequest $request, BlogCategory $blogCategory)
    {
        // Update non-translatable fields
        $blogCategory->update($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $blogCategory->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $blogCategory->save();
        
        // Handle relationships
        $blogCategory->seo ? $blogCategory->seo->update($request->get('seo')) :
            $blogCategory->seo()->create($request->get('seo'));
        $blogCategory->relatedTours()->sync($request->get('related_tours'));
        session()->flash('message', 'Blog Category Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();
        return response()->json([
            'message' => 'Blog Category Deleted Successfully!'
        ]);
    }
}
