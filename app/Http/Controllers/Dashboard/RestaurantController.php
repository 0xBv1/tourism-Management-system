<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\RestaurantDataTable;
use App\Http\Requests\Dashboard\RestaurantRequest;
use App\Models\Restaurant;
use App\Models\City;
use App\Enums\ResourceStatus;

class RestaurantController extends Controller
{
    public function index(RestaurantDataTable $dataTable)
    {
        return $dataTable->render('dashboard.restaurants.index');
    }

    public function create()
    {
        $cities = City::all();
        return view('dashboard.restaurants.create', compact('cities'));
    }

    public function store(RestaurantRequest $request)
    {
        $restaurant = Restaurant::create($request->getSanitized());
        
        session()->flash('message', 'Restaurant Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.restaurants.edit', $restaurant);
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->load(['city', 'bookings.bookingFile']);
        return view('dashboard.restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant)
    {
        $cities = City::all();
        return view('dashboard.restaurants.edit', compact('restaurant', 'cities'));
    }

    public function update(RestaurantRequest $request, Restaurant $restaurant)
    {
        $restaurant->update($request->getSanitized());
        
        session()->flash('message', 'Restaurant Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return response()->json([
            'message' => 'Restaurant Deleted Successfully!'
        ]);
    }
}
