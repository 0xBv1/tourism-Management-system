<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\HotelDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HotelRequest;
use App\Models\Hotel;
use App\Models\City;
use App\Enums\ResourceStatus;

class HotelController extends Controller
{
    public function index(HotelDataTable $dataTable)
    {
        return $dataTable->render('dashboard.hotels.index');
    }

    public function create()
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.hotels.create', compact('cities', 'statuses'));
    }

    public function store(HotelRequest $request)
    {
        $hotel = Hotel::create($request->getSanitized());
        
        session()->flash('message', 'Hotel Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.hotels.edit', $hotel);
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['city', 'bookings.bookingFile']);
        return view('dashboard.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel)
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.hotels.edit', compact('hotel', 'cities', 'statuses'));
    }

    public function update(HotelRequest $request, Hotel $hotel)
    {
        $hotel->update($request->getSanitized());
        
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

    public function calendar()
    {
        $hotels = Hotel::with('city')->get();
        return view('dashboard.hotels.calendar', compact('hotels'));
    }
}




