<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('supplier.services.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supplier.services.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Basic service creation logic
        return redirect()->route('supplier.services.index')
            ->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('supplier.services.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('supplier.services.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Basic service update logic
        return redirect()->route('supplier.services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Basic service deletion logic
        return redirect()->route('supplier.services.index')
            ->with('success', 'Service deleted successfully!');
    }
}
