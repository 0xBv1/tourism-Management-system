<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\DataTables\DahabiaDataTable;
use App\Http\Requests\Dashboard\DahabiaRequest;
use App\Models\Dahabia;
use App\Models\City;
use App\Enums\ResourceStatus;

class DahabiaController extends Controller
{
    public function index(DahabiaDataTable $dataTable)
    {
        return $dataTable->render('dashboard.dahabias.index');
    }

    public function create()
    {
        $cities = City::all();
        return view('dashboard.dahabias.create', compact('cities'));
    }

    public function store(DahabiaRequest $request)
    {
        $dahabia = Dahabia::create($request->getSanitized());
        
        session()->flash('message', 'Dahabia Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.dahabias.edit', $dahabia);
    }

    public function show(Dahabia $dahabia)
    {
        $dahabia->load(['city', 'bookings.bookingFile']);
        return view('dashboard.dahabias.show', compact('dahabia'));
    }

    public function edit(Dahabia $dahabia)
    {
        $cities = City::all();
        return view('dashboard.dahabias.edit', compact('dahabia', 'cities'));
    }

    public function update(DahabiaRequest $request, Dahabia $dahabia)
    {
        $dahabia->update($request->getSanitized());
        
        session()->flash('message', 'Dahabia Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }

    public function destroy(Dahabia $dahabia)
    {
        $dahabia->delete();
        return response()->json([
            'message' => 'Dahabia Deleted Successfully!'
        ]);
    }
}
