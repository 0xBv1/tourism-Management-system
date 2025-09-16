<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\SupplierHotel;
use App\Models\SupplierTrip;
use App\Models\SupplierTour;
use App\Models\SupplierTransport;
use App\Models\SupplierHotelBooking;
use App\Models\SupplierTripBooking;
use App\Models\SupplierTourBooking;
use App\Models\SupplierTransportBooking;

class DashboardController extends Controller
{

    /**
     * Display the supplier dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create')
                ->with('error', 'Please complete your supplier profile first.');
        }

        // Get statistics
        $stats = $this->getDashboardStats($supplier);

        return view('dashboard.supplier.dashboard', compact('supplier', 'stats'));
    }

    /**
     * Get dashboard statistics for the supplier.
     */
    private function getDashboardStats(Supplier $supplier)
    {
        // Service counts
        $hotelsCount = $supplier->hotels()->count();
        $tripsCount = $supplier->trips()->count();
        $toursCount = $supplier->tours()->count();
        $transportsCount = $supplier->transports()->count();

        // Booking counts
        $hotelBookingsCount = SupplierHotelBooking::where('supplier_id', $supplier->id)->count();
        $tripBookingsCount = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->count();
        $tourBookingsCount = SupplierTourBooking::where('supplier_id', $supplier->id)->count();
        $transportBookingsCount = SupplierTransportBooking::where('supplier_id', $supplier->id)->count();

        // Recent bookings
        $recentHotelBookings = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->with(['hotel', 'client'])
            ->latest()
            ->take(5)
            ->get();

        $recentTripBookings = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->with(['supplierTrip', 'user'])
            ->select('supplier_trip_bookings.*')
            ->latest('supplier_trip_bookings.created_at')
            ->take(5)
            ->get();

        $recentTourBookings = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->with(['tour', 'client'])
            ->latest()
            ->take(5)
            ->get();

        $recentTransportBookings = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->with(['transport', 'client'])
            ->latest()
            ->take(5)
            ->get();

        // Pending approvals
        $pendingHotels = $supplier->hotels()->where('approved', false)->count();
        $pendingTrips = $supplier->trips()->where('approved', false)->count();
        $pendingTours = $supplier->tours()->where('approved', false)->count();
        $pendingTransports = $supplier->transports()->where('approved', false)->count();

        return [
            'services' => [
                'hotels' => $hotelsCount,
                'trips' => $tripsCount,
                'tours' => $toursCount,
                'transports' => $transportsCount,
            ],
            'bookings' => [
                'hotels' => $hotelBookingsCount,
                'trips' => $tripBookingsCount,
                'tours' => $tourBookingsCount,
                'transports' => $transportBookingsCount,
            ],
            'recent_bookings' => [
                'hotels' => $recentHotelBookings,
                'trips' => $recentTripBookings,
                'tours' => $recentTourBookings,
                'transports' => $recentTransportBookings,
            ],
            'pending_approvals' => [
                'hotels' => $pendingHotels,
                'trips' => $pendingTrips,
                'tours' => $pendingTours,
                'transports' => $pendingTransports,
            ],
            'wallet_balance' => $supplier->formatted_wallet_balance,
            'commission_rate' => $supplier->formatted_commission_rate,
        ];
    }
}
