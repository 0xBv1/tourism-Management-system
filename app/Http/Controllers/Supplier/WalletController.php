<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\DataTables\SupplierWalletTransactionDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\SupplierHotelBooking;
use App\Models\SupplierTripBooking;
use App\Models\SupplierTourBooking;
use App\Models\SupplierTransportBooking;

class WalletController extends Controller
{
 

    /**
     * Display the supplier wallet.
     */
    public function index(SupplierWalletTransactionDataTable $dataTable)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        // Get wallet statistics
        $stats = $this->getWalletStats($supplier);

        return $dataTable->render('supplier.wallet.index', compact('supplier', 'stats'));
    }

    /**
     * Get wallet statistics for the supplier.
     */
    private function getWalletStats(Supplier $supplier)
    {
        // Total earnings from all services
        $hotelEarnings = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('supplier_amount');

        $tripEarnings = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->where('supplier_trip_bookings.status', 'confirmed')
            ->sum('supplier_trip_bookings.total_price');

        $tourEarnings = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('supplier_amount');

        $transportEarnings = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('supplier_amount');

        $totalEarnings = $hotelEarnings + $tripEarnings + $tourEarnings + $transportEarnings;

        // Total commissions paid
        $hotelCommissions = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('commission_amount');

        $tripCommissions = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->where('supplier_trip_bookings.status', 'confirmed')
            ->sum(DB::raw('0')); // Trip bookings don't have commission_amount field

        $tourCommissions = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('commission_amount');

        $transportCommissions = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->where('status', 'confirmed')
            ->sum('commission_amount');

        $totalCommissions = $hotelCommissions + $tripCommissions + $tourCommissions + $transportCommissions;

        // Pending amounts
        $pendingHotelAmount = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->sum('supplier_amount');

        $pendingTripAmount = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->where('supplier_trip_bookings.status', 'pending')
            ->sum('supplier_trip_bookings.total_price');

        $pendingTourAmount = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->sum('supplier_amount');

        $pendingTransportAmount = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->where('status', 'pending')
            ->sum('supplier_amount');

        $totalPending = $pendingHotelAmount + $pendingTripAmount + $pendingTourAmount + $pendingTransportAmount;

        return [
            'current_balance' => $supplier->wallet_balance,
            'total_earnings' => $totalEarnings,
            'total_commissions' => $totalCommissions,
            'total_pending' => $totalPending,
            'commission_rate' => $supplier->commission_rate,
            'by_service' => [
                'hotels' => [
                    'earnings' => $hotelEarnings,
                    'commissions' => $hotelCommissions,
                    'pending' => $pendingHotelAmount,
                ],
                'trips' => [
                    'earnings' => $tripEarnings,
                    'commissions' => $tripCommissions,
                    'pending' => $pendingTripAmount,
                ],
                'tours' => [
                    'earnings' => $tourEarnings,
                    'commissions' => $tourCommissions,
                    'pending' => $pendingTourAmount,
                ],
                'transports' => [
                    'earnings' => $transportEarnings,
                    'commissions' => $transportCommissions,
                    'pending' => $pendingTransportAmount,
                ],
            ],
        ];
    }

    /**
     * Get recent transactions for the supplier.
     */
    private function getRecentTransactions(Supplier $supplier)
    {
        $hotelBookings = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->with(['hotel', 'client'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'Hotel Booking',
                    'service_name' => $booking->hotel->name ?? 'N/A',
                    'client_name' => $booking->client->name ?? 'N/A',
                    'amount' => $booking->supplier_amount,
                    'commission' => $booking->commission_amount,
                    'status' => $booking->status,
                    'date' => $booking->created_at,
                    'reference' => 'HB-' . $booking->id,
                ];
            });

        $tripBookings = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->with(['supplierTrip', 'user'])
            ->select('supplier_trip_bookings.*')
            ->latest('supplier_trip_bookings.created_at')
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'Trip Booking',
                    'service_name' => $booking->supplierTrip->trip_name ?? 'N/A',
                    'client_name' => $booking->user->name ?? 'N/A',
                    'amount' => $booking->total_price,
                    'commission' => 0, // Trip bookings don't have commission
                    'status' => $booking->status,
                    'date' => $booking->created_at,
                    'reference' => 'TB-' . $booking->id,
                ];
            });

        $tourBookings = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->with(['tour', 'client'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'Tour Booking',
                    'service_name' => $booking->tour->title ?? 'N/A',
                    'client_name' => $booking->client->name ?? 'N/A',
                    'amount' => $booking->supplier_amount,
                    'commission' => $booking->commission_amount,
                    'status' => $booking->status,
                    'date' => $booking->created_at,
                    'reference' => 'TOB-' . $booking->id,
                ];
            });

        $transportBookings = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->with(['transport', 'client'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'Transport Booking',
                    'service_name' => $booking->transport->name ?? 'N/A',
                    'client_name' => $booking->client->name ?? 'N/A',
                    'amount' => $booking->supplier_amount,
                    'commission' => $booking->commission_amount,
                    'status' => $booking->status,
                    'date' => $booking->created_at,
                    'reference' => 'TRB-' . $booking->id,
                ];
            });

        // Merge and sort by date
        $allTransactions = $hotelBookings->concat($tripBookings)
            ->concat($tourBookings)
            ->concat($transportBookings)
            ->sortByDesc('date')
            ->take(20);

        return $allTransactions;
    }

    /**
     * Show transaction details.
     */
    public function showTransaction($type, $id)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        $transaction = null;

        switch ($type) {
            case 'hotel':
                $transaction = SupplierHotelBooking::where('supplier_id', $supplier->id)
                    ->with(['hotel', 'client'])
                    ->findOrFail($id);
                break;
            case 'trip':
                $transaction = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
                    ->where('supplier_trips.supplier_id', $supplier->id)
                    ->where('supplier_trip_bookings.id', $id)
                    ->with(['supplierTrip', 'user'])
                    ->select('supplier_trip_bookings.*')
                    ->firstOrFail();
                break;
            case 'tour':
                $transaction = SupplierTourBooking::where('supplier_id', $supplier->id)
                    ->with(['tour', 'client'])
                    ->findOrFail($id);
                break;
            case 'transport':
                $transaction = SupplierTransportBooking::where('supplier_id', $supplier->id)
                    ->with(['transport', 'client'])
                    ->findOrFail($id);
                break;
            default:
                abort(404);
        }

        return view('supplier.wallet.transaction', compact('transaction', 'type'));
    }
}
