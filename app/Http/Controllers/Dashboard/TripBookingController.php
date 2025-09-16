<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\TripBooking;
use Illuminate\Http\Request;

class TripBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = TripBooking::with(['trip', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.trip-bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.trip-bookings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'client_id' => 'required|exists:clients,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email',
            'passenger_phone' => 'required|string|max:20',
            'booking_date' => 'required|date',
            'selected_seats' => 'required|array',
            'selected_seats.*' => 'integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        TripBooking::create($validated);

        return redirect()->route('dashboard.trip-bookings.index')
            ->with('success', 'Trip booking created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TripBooking $tripBooking)
    {
        $tripBooking->load(['trip', 'client']);
        
        return view('dashboard.trip-bookings.show', compact('tripBooking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TripBooking $tripBooking)
    {
        return view('dashboard.trip-bookings.edit', compact('tripBooking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TripBooking $tripBooking)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'client_id' => 'required|exists:clients,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email',
            'passenger_phone' => 'required|string|max:20',
            'booking_date' => 'required|date',
            'selected_seats' => 'required|array',
            'selected_seats.*' => 'integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $tripBooking->update($validated);

        return redirect()->route('dashboard.trip-bookings.index')
            ->with('success', 'Trip booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TripBooking $tripBooking)
    {
        $tripBooking->delete();

        return redirect()->route('dashboard.trip-bookings.index')
            ->with('success', 'Trip booking deleted successfully!');
    }

    /**
     * Toggle booking status.
     */
    public function toggleStatus(TripBooking $tripBooking)
    {
        $tripBooking->update(['status' => $tripBooking->status === 'confirmed' ? 'cancelled' : 'confirmed']);

        $status = $tripBooking->status;
        return redirect()->back()->with('success', "Booking {$status} successfully!");
    }

    /**
     * Cancel booking.
     */
    public function cancel(TripBooking $tripBooking)
    {
        $tripBooking->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Export bookings.
     */
    public function export()
    {
        $bookings = TripBooking::with(['trip', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Basic CSV export logic
        $filename = 'trip_bookings_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response()->streamDownload(function () use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['ID', 'Trip', 'Client', 'Passenger', 'Email', 'Phone', 'Booking Date', 'Status', 'Total Price']);
            
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->trip->trip_name ?? 'N/A',
                    $booking->client->name ?? 'N/A',
                    $booking->passenger_name,
                    $booking->passenger_email,
                    $booking->passenger_phone,
                    $booking->booking_date,
                    $booking->status,
                    $booking->total_price,
                ]);
            }
            
            fclose($file);
        }, $filename);
    }
}
