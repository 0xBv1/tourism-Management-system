<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\RoomDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RoomRequest;
use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;

class RoomController extends Controller
{

    public function index(RoomDataTable $dataTable)
    {
        return $dataTable->render('dashboard.rooms.index');
    }

    public function store(RoomRequest $request)
    {
        // Create room with non-translatable fields
        $room = Room::create($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $room->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $room->save();
        
        // Handle relationships
        $room->amenities()->attach($request->get('amenities'));
        $room->seo()->create($request->get('seo'));
        session()->flash('message', 'Room Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.rooms.edit', $room);
    }

    public function create()
    {
        $hotels = Hotel::orderByTranslation('name')->get()->mapWithKeys(function($hotel) {
            return [$hotel->id => $hotel->translate('en')->name];
        })->toArray();
        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->translate('en')->name];
        })->toArray();
        return view('dashboard.rooms.create', compact('hotels', 'amenities'));
    }

    public function show(Room $room)
    {
        //
    }

    public function edit(Room $room)
    {
        $room->load('amenities');
        $hotels = Hotel::orderByTranslation('name')->get()->mapWithKeys(function($hotel) {
            return [$hotel->id => $hotel->translate('en')->name];
        })->toArray();
        $amenities = Amenity::orderByTranslation('name')->get()->mapWithKeys(function($amenity) {
            return [$amenity->id => $amenity->translate('en')->name];
        })->toArray();
        return view('dashboard.rooms.edit', compact('room', 'amenities', 'hotels'));
    }

    public function update(RoomRequest $request, Room $room)
    {
        // Update non-translatable fields
        $room->update($request->getSanitized());
        
        // Handle translatable fields
        $translatableData = $request->getTranslatableData();
        foreach ($translatableData as $locale => $data) {
            foreach ($data as $field => $value) {
                if ($value !== null && $value !== '') {
                    $room->translateOrNew($locale)->$field = $value;
                }
            }
        }
        $room->save();
        
        // Handle relationships
        $room->amenities()->sync($request->get('amenities'));
        $room->seo ?
            $room->seo->update($request->get('seo')) :
            $room->seo()->create($request->get('seo'));
        session()->flash('message', 'Room Updated Successfully!');
        session()->flash('type', 'success');
        return back();
    }


    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json([
            'message' => 'Room Deleted Successfully!'
        ]);
    }
}
