<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\VehicleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\VehicleRequest;
use App\Models\Vehicle;
use App\Models\City;
use App\Enums\ResourceStatus;

class VehicleController extends Controller
{
    public function index(VehicleDataTable $dataTable)
    {
        return $dataTable->render('dashboard.vehicles.index');
    }

    public function create()
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.vehicles.create', compact('cities', 'statuses'));
    }

    public function store(VehicleRequest $request)
    {
        $vehicle = Vehicle::create($request->getSanitized());
        
        session()->flash('message', 'Vehicle Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.vehicles.edit', $vehicle);
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['city', 'bookings.bookingFile']);
        return view('dashboard.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.vehicles.edit', compact('vehicle', 'cities', 'statuses'));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->getSanitized());
        
        session()->flash('message', 'Vehicle Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return response()->json([
            'message' => 'Vehicle Deleted Successfully!'
        ]);
    }

    public function calendar()
    {
        $vehicles = Vehicle::with('city')->get();
        return view('dashboard.vehicles.calendar', compact('vehicles'));
    }
}




