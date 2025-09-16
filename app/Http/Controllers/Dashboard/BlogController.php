<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\BlogStatus;
use App\Events\ResourceCreatedEvent;
use App\Models\Blog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\BlogRequest;
use App\DataTables\BlogDataTable;
use App\Models\BlogCategory;

class BlogController extends Controller
{

    public function index(BlogDataTable $dataTable)
    {
        return $dataTable->render('dashboard.blogs.index');
    }


    public function create()
    {
        $categories = BlogCategory::orderByTranslation('title')->get()->mapWithKeys(function($category) {
            return [$category->id => $category->translate('en')->title];
        })->toArray();
        return view('dashboard.blogs.create', compact('categories'));
    }


    public function store(BlogRequest $request)
    {
        // Create blog with non-translatable fields
        $blog = Blog::create($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $blog->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $blog->save();
        
        // Handle relationships
        $blog->seo()->create($request->get('seo'));
        $blog->relatedTours()->sync($request->get('related_tours'));
        $blog->categories()->attach($request->get('categories'));
        session()->flash('message', 'Blog Created Successfully!');
        session()->flash('type', 'success');
        ResourceCreatedEvent::dispatch($blog);
        return redirect()->route('dashboard.blogs.edit', $blog);
    }


    public function show(Blog $blog)
    {
        //
    }


    public function edit(Blog $blog)
    {
        $categories = BlogCategory::orderByTranslation('title')->get()->mapWithKeys(function($category) {
            return [$category->id => $category->translate('en')->title];
        })->toArray();
        return view('dashboard.blogs.edit', compact('blog', 'categories'));
    }


    public function update(BlogRequest $request, Blog $blog)
    {
        // Update non-translatable fields
        $blog->update($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $blog->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $blog->save();
        
        if ($request->filled('action')) {
            return response()->json([
                'message' => 'Blog Updated Successfully'
            ]);
        }
        
        // Handle relationships
        $blog->seo ?
            $blog->seo->update($request->get('seo')) :
            $blog->seo()->create($request->get('seo'));
        $blog->categories()->sync($request->get('categories'));
        $blog->relatedTours()->sync($request->get('related_tours'));
        session()->flash('message', 'Blog Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json([
            'message' => 'Blog Deleted Successfully!'
        ]);
    }
}
