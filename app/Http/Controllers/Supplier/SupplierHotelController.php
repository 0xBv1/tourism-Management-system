<?php

namespace App\Http\Controllers\Supplier;

use App\DataTables\SupplierHotelDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\SupplierHotelRequest;
use App\Models\Amenity;
use App\Models\SupplierHotel;
use App\Models\Destination;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierHotelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SupplierHotelDataTable $dataTable)
    {
        $this->authorize('supplier.hotels.list');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        return $dataTable->render('dashboard.supplier.hotels.index', compact('supplier'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('supplier.hotels.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->name];
        })->toArray();
        $destinations = Destination::all();
        $cities = City::orderBy('name')->pluck('name', 'name')->toArray();

        return view('dashboard.supplier.hotels.create', compact('supplier', 'amenities', 'destinations', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierHotelRequest $request)
    {
        $this->authorize('supplier.hotels.create');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.dashboard')->with('error', 'Supplier profile not found.');
        }

        $data = $request->getSanitized();
        $data['supplier_id'] = $supplier->id;

        // Generate slug automatically if empty
        if (empty($data['slug']) && !empty($data[config('app.locale')]['name'])) {
            $data['slug'] = SupplierHotel::generateSlug($data[config('app.locale')]['name']);
        }

        $hotel = SupplierHotel::create($data);
        $hotel->seo()->create($request->get('seo'));
        $hotel->amenities()->attach($request->get('amenities'));

        session()->flash('message', 'Hotel created successfully! It will be reviewed by admin before approval.');
        session()->flash('type', 'success');

        return redirect()->route('supplier.hotels.edit', $hotel);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierHotel $hotel)
    {
        $this->authorize('supplier.hotels.view');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $hotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        return view('dashboard.supplier.hotels.show', compact('supplier', 'hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierHotel $hotel)
    {
        $this->authorize('supplier.hotels.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $hotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        $hotel->load('amenities');
        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->name];
        })->toArray();
        $destinations = Destination::all();
        $cities = City::orderBy('name')->pluck('name', 'name')->toArray();

        return view('dashboard.supplier.hotels.edit', compact('supplier', 'hotel', 'amenities', 'destinations', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierHotelRequest $request, SupplierHotel $hotel)
    {
        $this->authorize('supplier.hotels.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $hotel->supplier_id !== $supplier->id) {
            return redirect()->route('supplier.dashboard')->with('error', 'Access denied.');
        }

        $data = $request->getSanitized();

        // Generate slug automatically if empty
        if (empty($data['slug']) && !empty($data[config('app.locale')]['name'])) {
            $data['slug'] = SupplierHotel::generateSlug($data[config('app.locale')]['name']);
        }

        $hotel->update($data);
        $hotel->amenities()->sync($request->get('amenities'));
        $hotel->seo ?
            $hotel->seo->update($request->get('seo')) :
            $hotel->seo()->create($request->get('seo'));

        session()->flash('message', 'Hotel updated successfully!');
        session()->flash('type', 'success');

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierHotel $hotel)
    {
        $this->authorize('supplier.hotels.delete');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $hotel->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        // Delete images
        if ($hotel->featured_image) {
            Storage::disk('public')->delete($hotel->featured_image);
        }
        if ($hotel->banner) {
            Storage::disk('public')->delete($hotel->banner);
        }
        if ($hotel->gallery) {
            foreach ($hotel->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $hotel->delete();

        return response()->json([
            'message' => 'Hotel deleted successfully!'
        ]);
    }

    /**
     * Toggle hotel status.
     */
    public function toggleStatus(SupplierHotel $hotel)
    {
        $this->authorize('supplier.hotels.edit');
        
        $user = auth()->user();
        $supplier = $user->supplier;

        if (!$supplier || $hotel->supplier_id !== $supplier->id) {
            return response()->json(['error' => 'Access denied.'], 403);
        }

        $hotel->update(['enabled' => !$hotel->enabled]);

        return response()->json([
            'success' => true,
            'message' => 'Hotel status updated successfully!',
            'enabled' => $hotel->enabled
        ]);
    }
}