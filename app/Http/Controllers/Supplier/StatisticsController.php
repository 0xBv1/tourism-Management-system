<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Supplier;
use App\Models\SupplierHotelBooking;
use App\Models\SupplierTripBooking;
use App\Models\SupplierTourBooking;
use App\Models\SupplierTransportBooking;
use Carbon\Carbon;

class StatisticsController extends Controller
{
 

    /**
     * Display the supplier statistics.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Get statistics
        $stats = $this->getStatistics($supplier, $startDate, $endDate);

        return view('dashboard.supplier.statistics.index', compact('supplier', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Get statistics for the supplier.
     */
    private function getStatistics(Supplier $supplier, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Booking statistics
        $hotelBookings = SupplierHotelBooking::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $tripBookings = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
            ->where('supplier_trips.supplier_id', $supplier->id)
            ->whereBetween('supplier_trip_bookings.created_at', [$start, $end])
            ->select('supplier_trip_bookings.*')
            ->get();

        $tourBookings = SupplierTourBooking::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $transportBookings = SupplierTransportBooking::where('supplier_id', $supplier->id)
            ->whereBetween('created_at', [$start, $end])
            ->get();

        // Revenue statistics
        $hotelRevenue = $hotelBookings->where('status', 'confirmed')->sum('supplier_amount');
        $tripRevenue = $tripBookings->where('status', 'confirmed')->sum('total_price');
        $tourRevenue = $tourBookings->where('status', 'confirmed')->sum('supplier_amount');
        $transportRevenue = $transportBookings->where('status', 'confirmed')->sum('supplier_amount');

        $totalRevenue = $hotelRevenue + $tripRevenue + $tourRevenue + $transportRevenue;

        // Commission statistics
        $hotelCommissions = $hotelBookings->where('status', 'confirmed')->sum('commission_amount');
        $tripCommissions = 0; // Trip bookings don't have commission_amount field
        $tourCommissions = $tourBookings->where('status', 'confirmed')->sum('commission_amount');
        $transportCommissions = $transportBookings->where('status', 'confirmed')->sum('commission_amount');

        $totalCommissions = $hotelCommissions + $tripCommissions + $tourCommissions + $transportCommissions;

        // Booking counts by status
        $hotelBookingStats = $this->getBookingStatsByStatus($hotelBookings);
        $tripBookingStats = $this->getBookingStatsByStatus($tripBookings);
        $tourBookingStats = $this->getBookingStatsByStatus($tourBookings);
        $transportBookingStats = $this->getBookingStatsByStatus($transportBookings);

        // Daily revenue chart data
        $dailyRevenue = $this->getDailyRevenueData($supplier, $start, $end);

        // Service performance
        $servicePerformance = [
            'hotels' => [
                'bookings' => $hotelBookings->count(),
                'revenue' => $hotelRevenue,
                'commissions' => $hotelCommissions,
                'avg_booking_value' => $hotelBookings->count() > 0 ? $hotelRevenue / $hotelBookings->count() : 0,
            ],
            'trips' => [
                'bookings' => $tripBookings->count(),
                'revenue' => $tripRevenue,
                'commissions' => $tripCommissions,
                'avg_booking_value' => $tripBookings->count() > 0 ? $tripRevenue / $tripBookings->count() : 0,
            ],
            'tours' => [
                'bookings' => $tourBookings->count(),
                'revenue' => $tourRevenue,
                'commissions' => $tourCommissions,
                'avg_booking_value' => $tourBookings->count() > 0 ? $tourRevenue / $tourBookings->count() : 0,
            ],
            'transports' => [
                'bookings' => $transportBookings->count(),
                'revenue' => $transportRevenue,
                'commissions' => $transportCommissions,
                'avg_booking_value' => $transportBookings->count() > 0 ? $transportRevenue / $transportBookings->count() : 0,
            ],
        ];

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $start->diffInDays($end) + 1,
            ],
            'overview' => [
                'total_bookings' => $hotelBookings->count() + $tripBookings->count() + $tourBookings->count() + $transportBookings->count(),
                'total_revenue' => $totalRevenue,
                'total_commissions' => $totalCommissions,
                'net_revenue' => $totalRevenue - $totalCommissions,
                'avg_daily_revenue' => $start->diffInDays($end) > 0 ? $totalRevenue / ($start->diffInDays($end) + 1) : 0,
            ],
            'revenue_by_service' => [
                'hotels' => $hotelRevenue,
                'trips' => $tripRevenue,
                'tours' => $tourRevenue,
                'transports' => $transportRevenue,
            ],
            'commissions_by_service' => [
                'hotels' => $hotelCommissions,
                'trips' => $tripCommissions,
                'tours' => $tourCommissions,
                'transports' => $transportCommissions,
            ],
            'booking_stats' => [
                'hotels' => $hotelBookingStats,
                'trips' => $tripBookingStats,
                'tours' => $tourBookingStats,
                'transports' => $transportBookingStats,
            ],
            'service_performance' => $servicePerformance,
            'daily_revenue' => $dailyRevenue,
        ];
    }

    /**
     * Get booking statistics by status.
     */
    private function getBookingStatsByStatus($bookings)
    {
        return [
            'pending' => $bookings->where('status', 'pending')->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
        ];
    }

    /**
     * Get daily revenue data for charts.
     */
    private function getDailyRevenueData(Supplier $supplier, Carbon $start, Carbon $end)
    {
        $data = [];
        $current = $start->copy();

        while ($current <= $end) {
            $date = $current->format('Y-m-d');
            
            $hotelRevenue = SupplierHotelBooking::where('supplier_id', $supplier->id)
                ->where('status', 'confirmed')
                ->whereDate('created_at', $date)
                ->sum('supplier_amount');

            $tripRevenue = SupplierTripBooking::join('supplier_trips', 'supplier_trip_bookings.supplier_trip_id', '=', 'supplier_trips.id')
                ->where('supplier_trips.supplier_id', $supplier->id)
                ->where('supplier_trip_bookings.status', 'confirmed')
                ->whereDate('supplier_trip_bookings.created_at', $date)
                ->sum('supplier_trip_bookings.total_price');

            $tourRevenue = SupplierTourBooking::where('supplier_id', $supplier->id)
                ->where('status', 'confirmed')
                ->whereDate('created_at', $date)
                ->sum('supplier_amount');

            $transportRevenue = SupplierTransportBooking::where('supplier_id', $supplier->id)
                ->where('status', 'confirmed')
                ->whereDate('created_at', $date)
                ->sum('supplier_amount');

            $data[] = [
                'date' => $date,
                'hotels' => $hotelRevenue,
                'trips' => $tripRevenue,
                'tours' => $tourRevenue,
                'transports' => $transportRevenue,
                'total' => $hotelRevenue + $tripRevenue + $tourRevenue + $transportRevenue,
            ];

            $current->addDay();
        }

        return $data;
    }

    /**
     * Export statistics as CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $supplier = $user->supplier;

        if (!$supplier) {
            return redirect()->route('supplier.profile.create');
        }

        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $stats = $this->getStatistics($supplier, $startDate, $endDate);

        $filename = 'supplier_statistics_' . $startDate . '_to_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stats) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, ['Metric', 'Value']);
            
            // Write overview data
            fputcsv($file, ['Total Bookings', $stats['overview']['total_bookings']]);
            fputcsv($file, ['Total Revenue', $stats['overview']['total_revenue']]);
            fputcsv($file, ['Total Commissions', $stats['overview']['total_commissions']]);
            fputcsv($file, ['Net Revenue', $stats['overview']['net_revenue']]);
            fputcsv($file, ['Average Daily Revenue', $stats['overview']['avg_daily_revenue']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
