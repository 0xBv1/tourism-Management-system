<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripBooking;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trip::with(['driver', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.trips.index', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tripTypes = [
            'one_way' => 'One Way',
            'round_trip' => 'Round Trip',
        ];

        $cities = \App\Models\City::orderBy('name')->pluck('name', 'id')->toArray();
        
        $amenities = [
            'WiFi' => 'WiFi',
            'Air Conditioning' => 'Air Conditioning',
            'Restroom' => 'Restroom',
            'Refreshments' => 'Refreshments',
            'Entertainment' => 'Entertainment',
            'Reclining Seats' => 'Reclining Seats',
            'Meal Included' => 'Meal Included',
        ];

        return view('dashboard.trips.create', compact('tripTypes', 'cities', 'amenities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_type' => 'required|in:one_way,round_trip',
            'departure_city_id' => 'required|exists:cities,id',
            'arrival_city_id' => 'required|exists:cities,id',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after:travel_date',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'seat_price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'additional_notes' => 'nullable|string',
            'enabled' => 'boolean',
        ]);

        // Generate trip name from cities
        $departureCity = \App\Models\City::find($validated['departure_city_id']);
        $arrivalCity = \App\Models\City::find($validated['arrival_city_id']);
        $tripName = $departureCity->name . ' to ' . $arrivalCity->name . ' ' . ucfirst($validated['trip_type']);

        $tripData = [
            'trip_type' => $validated['trip_type'],
            'departure_city_id' => $validated['departure_city_id'],
            'arrival_city_id' => $validated['arrival_city_id'],
            'travel_date' => $validated['travel_date'],
            'return_date' => $validated['return_date'],
            'departure_time' => $validated['departure_time'],
            'arrival_time' => $validated['arrival_time'],
            'seat_price' => $validated['seat_price'],
            'total_seats' => $validated['total_seats'],
            'available_seats' => $validated['total_seats'],
            'amenities' => $validated['amenities'] ? json_encode($validated['amenities']) : null,
            'additional_notes' => $validated['additional_notes'],
            'enabled' => $validated['enabled'] ?? true,
            'trip_name' => $tripName,
        ];

        Trip::create($tripData);

        return redirect()->route('dashboard.trips.index')
            ->with('success', 'Trip created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        $trip->load(['driver', 'vehicle', 'bookings']);
        
        return view('dashboard.trips.show', compact('trip'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Trip $trip)
    {
        return view('dashboard.trips.edit', compact('trip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'trip_name' => 'required|string|max:255',
            'trip_type' => 'required|in:one_way,round_trip,special_discount',
            'departure_city' => 'required|string|max:255',
            'arrival_city' => 'required|string|max:255',
            'travel_date' => 'required|date',
            'return_date' => 'nullable|date|after:travel_date',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i',
            'seat_price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'driver_id' => 'required|exists:transport_drivers,id',
            'vehicle_id' => 'required|exists:transport_vehicles,id',
        ]);

        $trip->update($validated);

        return redirect()->route('dashboard.trips.index')
            ->with('success', 'Trip updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        $trip->delete();

        return redirect()->route('dashboard.trips.index')
            ->with('success', 'Trip deleted successfully!');
    }

    /**
     * Show trip bookings.
     */
    public function tripBookings(Trip $trip)
    {
        $bookings = TripBooking::where('trip_id', $trip->id)
            ->with(['client', 'trip'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.trips.bookings', compact('trip', 'bookings'));
    }

    /**
     * Toggle trip status.
     */
    public function toggleStatus(Trip $trip)
    {
        $trip->update(['enabled' => !$trip->enabled]);

        $status = $trip->enabled ? 'enabled' : 'disabled';
        return redirect()->back()->with('success', "Trip {$status} successfully!");
    }

    /**
     * Get trip details.
     */
    public function getTripDetails(Trip $trip)
    {
        $trip->load(['driver', 'vehicle', 'bookings']);
        
        return response()->json($trip);
    }
}
