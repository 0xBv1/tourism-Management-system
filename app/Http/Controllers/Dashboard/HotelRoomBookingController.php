<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\HotelRoomBooking;
use Illuminate\Http\Request;

class HotelRoomBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = HotelRoomBooking::with(['hotel', 'room', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.hotel-room-bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.hotel-room-bookings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'client_id' => 'required|exists:clients,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        HotelRoomBooking::create($validated);

        return redirect()->route('dashboard.hotel_room_bookings.index')
            ->with('success', 'Hotel room booking created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(HotelRoomBooking $hotelRoomBooking)
    {
        $hotelRoomBooking->load(['hotel', 'room', 'client']);
        
        return view('dashboard.hotel-room-bookings.show', compact('hotelRoomBooking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HotelRoomBooking $hotelRoomBooking)
    {
        return view('dashboard.hotel-room-bookings.edit', compact('hotelRoomBooking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HotelRoomBooking $hotelRoomBooking)
    {
        $validated = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'client_id' => 'required|exists:clients,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'guests' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $hotelRoomBooking->update($validated);

        return redirect()->route('dashboard.hotel_room_bookings.index')
            ->with('success', 'Hotel room booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HotelRoomBooking $hotelRoomBooking)
    {
        $hotelRoomBooking->delete();

        return redirect()->route('dashboard.hotel_room_bookings.index')
            ->with('success', 'Hotel room booking deleted successfully!');
    }
}
