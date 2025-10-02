<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\NileCruiseDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\NileCruiseRequest;
use App\Models\NileCruise;
use App\Models\City;
use App\Models\ResourceBooking;
use Illuminate\Http\Request;

class NileCruiseController extends Controller
{
    public function index(NileCruiseDataTable $dataTable)
    {
        $cities = City::orderBy('name')->get();
        
        return $dataTable->render('dashboard.nile-cruises.index', compact('cities'));
    }

    public function create()
    {
        $cities = City::orderBy('name')->get();
        
        return view('dashboard.nile-cruises.create', compact('cities'));
    }

    public function store(NileCruiseRequest $request)
    {
        NileCruise::create($request->getSanitized());

        return redirect()->route('dashboard.nile-cruises.index')
                       ->with('success', __('Nile Cruise created successfully.'));
    }

    public function show(NileCruise $nileCruise)
    {
        $nileCruise->load(['city', 'bookings']);

        return view('dashboard.nile-cruises.show', compact('nileCruise'));
    }

    public function edit(NileCruise $nileCruise)
    {
        $cities = City::orderBy('name')->get();

        return view('dashboard.nile-cruises.edit', compact('nileCruise', 'cities'));
    }

    public function update(NileCruiseRequest $request, NileCruise $nileCruise)
    {
        $nileCruise->update($request->getSanitized());

        return redirect()->route('dashboard.nile-cruises.index')
                       ->with('success', __('Nile Cruise updated successfully.'));
    }

    public function destroy(NileCruise $nileCruise)
    {
        // Check if there are any bookings for this Nile cruise
        $bookingsCount = ResourceBooking::where('resource_type', 'nile_cruise')
                                      ->where('resource_id', $nileCruise->id)
                                      ->count();

        if ($bookingsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete this Nile cruise. It has $bookingsCount related bookings."
            ], 422);
        }

        $nileCruise->delete();

        return response()->json([
            'success' => true,
            'message' => __('Nile Cruise deleted successfully.')
        ]);
    }
}
