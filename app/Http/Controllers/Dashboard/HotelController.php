<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\HotelDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HotelRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{

    public function index(HotelDataTable $dataTable)
    {
        return $dataTable->render('dashboard.hotels.index');
    }

    public function store(HotelRequest $request)
    {
        // Create hotel with non-translatable fields
        $hotel = Hotel::create($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $hotel->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $hotel->save();
        
        // Handle relationships
        $hotel->seo()->create($request->get('seo'));
        $hotel->amenities()->attach($request->get('amenities'));
        session()->flash('message', 'Hotel Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.hotels.edit', $hotel);
    }

    public function create()
    {
        $amenities = Amenity::all();
        $cities = DB::table('cities')->orderBy('name')->pluck('name', 'name')->toArray();
        return view('dashboard.hotels.create', compact('amenities', 'cities'));
    }

    public function show(Hotel $hotel)
    {
        //
    }


    public function edit(Hotel $hotel)
    {
        $hotel->load('amenities');
        $amenities = Amenity::all();
        $cities = DB::table('cities')->orderBy('name')->pluck('name', 'name')->toArray();
        return view('dashboard.hotels.edit', compact('hotel', 'amenities', 'cities'));
    }


    public function update(HotelRequest $request, Hotel $hotel)
    {
        // Update non-translatable fields
        $hotel->update($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $hotel->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $hotel->save();
        
        // Handle relationships
        $hotel->amenities()->sync($request->get('amenities'));
        $hotel->seo ?
            $hotel->seo->update($request->get('seo')) :
            $hotel->seo()->create($request->get('seo'));
        session()->flash('message', 'Hotel Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return response()->json([
            'message' => 'Hotel Deleted Successfully!'
        ]);
    }
}
