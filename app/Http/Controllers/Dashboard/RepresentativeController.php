<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\RepresentativeDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RepresentativeRequest;
use App\Models\Representative;
use App\Models\City;
use App\Enums\ResourceStatus;

class RepresentativeController extends Controller
{
    public function index(RepresentativeDataTable $dataTable)
    {
        return $dataTable->render('dashboard.representatives.index');
    }

    public function create()
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.representatives.create', compact('cities', 'statuses'));
    }

    public function store(RepresentativeRequest $request)
    {
        $representative = Representative::create($request->getSanitized());
        
        session()->flash('message', 'Representative Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.representatives.edit', $representative);
    }

    public function show(Representative $representative)
    {
        $representative->load(['city', 'bookings.bookingFile']);
        return view('dashboard.representatives.show', compact('representative'));
    }

    public function edit(Representative $representative)
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.representatives.edit', compact('representative', 'cities', 'statuses'));
    }

    public function update(RepresentativeRequest $request, Representative $representative)
    {
        $representative->update($request->getSanitized());
        
        session()->flash('message', 'Representative Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Representative $representative)
    {
        $representative->delete();
        return response()->json([
            'message' => 'Representative Deleted Successfully!'
        ]);
    }

    public function calendar()
    {
        $representatives = Representative::with('city')->get();
        return view('dashboard.representatives.calendar', compact('representatives'));
    }
}




