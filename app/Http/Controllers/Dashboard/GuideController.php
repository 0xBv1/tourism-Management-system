<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\GuideDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\GuideRequest;
use App\Models\Guide;
use App\Models\City;
use App\Enums\ResourceStatus;

class GuideController extends Controller
{
    public function index(GuideDataTable $dataTable)
    {
        return $dataTable->render('dashboard.guides.index');
    }

    public function create()
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.guides.create', compact('cities', 'statuses'));
    }

    public function store(GuideRequest $request)
    {
        $guide = Guide::create($request->getSanitized());
        
        session()->flash('message', 'Guide Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.guides.edit', $guide);
    }

    public function show(Guide $guide)
    {
        $guide->load(['city', 'bookings.bookingFile']);
        return view('dashboard.guides.show', compact('guide'));
    }

    public function edit(Guide $guide)
    {
        $cities = City::all();
        $statuses = ResourceStatus::options();
        return view('dashboard.guides.edit', compact('guide', 'cities', 'statuses'));
    }

    public function update(GuideRequest $request, Guide $guide)
    {
        $guide->update($request->getSanitized());
        
        session()->flash('message', 'Guide Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Guide $guide)
    {
        $guide->delete();
        return response()->json([
            'message' => 'Guide Deleted Successfully!'
        ]);
    }

    public function calendar()
    {
        $guides = Guide::with('city')->get();
        return view('dashboard.guides.calendar', compact('guides'));
    }
}




