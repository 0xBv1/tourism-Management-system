<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\BookingFile;
use App\Models\Payment;
use App\Models\User;
use App\Models\Client;
use App\Models\Hotel;
use App\Models\Vehicle;
use App\Models\Guide;
use App\Models\Representative;
use App\Models\ResourceBooking;
use App\Models\InquiryResource;
use App\Enums\InquiryStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * Show the main reports dashboard
     */
    public function index()
    {
        $this->authorize('reports.index');
        
        // Get summary statistics for the current month
        $currentMonth = now()->startOfMonth();
        $nextMonth = now()->endOfMonth();
        
        $summary = [
            'inquiries' => [
                'total' => Inquiry::count(),
                'this_month' => Inquiry::whereBetween('created_at', [$currentMonth, $nextMonth])->count(),
                'pending' => Inquiry::where('status', InquiryStatus::PENDING)->count(),
                'confirmed' => Inquiry::where('status', InquiryStatus::CONFIRMED)->count(),
            ],
            'bookings' => [
                'total' => BookingFile::count(),
                'this_month' => BookingFile::whereBetween('created_at', [$currentMonth, $nextMonth])->count(),
                'pending' => BookingFile::where('status', BookingStatus::PENDING)->count(),
                'confirmed' => BookingFile::where('status', BookingStatus::CONFIRMED)->count(),
                'completed' => BookingFile::where('status', BookingStatus::COMPLETED)->count(),
            ],
            'payments' => [
                'total' => Payment::count(),
                'this_month' => Payment::whereBetween('created_at', [$currentMonth, $nextMonth])->count(),
                'total_amount' => Payment::sum('amount'),
                'paid_amount' => Payment::where('status', PaymentStatus::PAID)->sum('amount'),
                'pending_amount' => Payment::where('status', PaymentStatus::PENDING)->sum('amount'),
            ],
            'clients' => [
                'total' => Client::count(),
                'this_month' => Client::whereBetween('created_at', [$currentMonth, $nextMonth])->count(),
            ],
        ];
        
        return view('dashboard.reports.index', compact('summary'));
    }

    /**
     * Inquiries Report
     */
    public function inquiries(Request $request)
    {
        $this->authorize('reports.inquiries');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $inquiriesQuery = Inquiry::with(['client', 'bookingFile'])
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        // Filter inquiries based on user role for reports
        if (auth()->user()->hasRole(['Reservation', 'Operation'])) {
            $inquiriesQuery->where('assigned_to', auth()->id());
        }
        
        $inquiries = $inquiriesQuery->orderBy('created_at', 'desc')->get();

        $statusBreakdown = InquiryStatus::cases();
        $statusData = [];
        foreach ($statusBreakdown as $status) {
            $statusData[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $inquiries->where('status', $status)->count(),
                'percentage' => $inquiries->count() > 0 ? round(($inquiries->where('status', $status)->count() / $inquiries->count()) * 100, 2) : 0,
            ];
        }

        $monthlyData = $this->getMonthlyData(Inquiry::class, $startDate, $endDate);
        $conversionRate = $this->calculateConversionRate($inquiries);

        return view('dashboard.reports.inquiries', compact(
            'inquiries', 
            'statusData', 
            'monthlyData', 
            'conversionRate',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Booking Files Report
     */
    public function bookings(Request $request)
    {
        $this->authorize('reports.bookings');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $bookings = BookingFile::with(['inquiry.client', 'payments'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $statusBreakdown = BookingStatus::cases();
        $statusData = [];
        foreach ($statusBreakdown as $status) {
            $statusData[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $bookings->where('status', $status)->count(),
                'percentage' => $bookings->count() > 0 ? round(($bookings->where('status', $status)->count() / $bookings->count()) * 100, 2) : 0,
            ];
        }

        $monthlyData = $this->getMonthlyData(BookingFile::class, $startDate, $endDate);
        $revenueData = $this->getRevenueData($bookings);

        return view('dashboard.reports.bookings', compact(
            'bookings', 
            'statusData', 
            'monthlyData', 
            'revenueData',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Finance Report
     */
    public function finance(Request $request)
    {
        $this->authorize('reports.finance');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $payments = Payment::with(['booking.inquiry.client'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $statusBreakdown = PaymentStatus::cases();
        $statusData = [];
        foreach ($statusBreakdown as $status) {
            $statusData[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $payments->where('status', $status)->count(),
                'amount' => $payments->where('status', $status)->sum('amount'),
                'percentage' => $payments->count() > 0 ? round(($payments->where('status', $status)->count() / $payments->count()) * 100, 2) : 0,
            ];
        }

        $monthlyData = $this->getMonthlyData(Payment::class, $startDate, $endDate);
        $gatewayData = $this->getGatewayData($payments);
        $revenueTrend = $this->getRevenueTrend($startDate, $endDate);

        return view('dashboard.reports.finance', compact(
            'payments', 
            'statusData', 
            'monthlyData', 
            'gatewayData',
            'revenueTrend',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Operational Report
     */
    public function operational(Request $request)
    {
        $this->authorize('reports.operational');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Resource utilization
        $hotelUtilization = $this->getResourceUtilization('hotel', $startDate, $endDate);
        $vehicleUtilization = $this->getResourceUtilization('vehicle', $startDate, $endDate);
        $guideUtilization = $this->getResourceUtilization('guide', $startDate, $endDate);
        $representativeUtilization = $this->getResourceUtilization('representative', $startDate, $endDate);

        // Staff performance
        $staffPerformance = $this->getStaffPerformance($startDate, $endDate);

        // Resource bookings
        $resourceBookings = ResourceBooking::with(['bookingFile.inquiry.client', 'hotel', 'vehicle', 'guide', 'representative'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->orderBy('start_date')
            ->get();

        return view('dashboard.reports.operational', compact(
            'hotelUtilization',
            'vehicleUtilization', 
            'guideUtilization',
            'representativeUtilization',
            'staffPerformance',
            'resourceBookings',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Performance Report
     */
    public function performance(Request $request)
    {
        $this->authorize('reports.performance');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // KPI calculations
        $kpis = $this->calculateKPIs($startDate, $endDate);
        
        // Trend analysis
        $trends = $this->getTrendAnalysis($startDate, $endDate);
        
        // Top performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate);
        
        // Conversion funnel
        $conversionFunnel = $this->getConversionFunnel($startDate, $endDate);

        return view('dashboard.reports.performance', compact(
            'kpis',
            'trends',
            'topPerformers',
            'conversionFunnel',
            'startDate', 
            'endDate'
        ));
    }

    /**
     * Inquiry Resources Report - Track resource assignments to inquiries
     */
    public function inquiryResources(Request $request)
    {
        $this->authorize('reports.inquiry-resources');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        // Get inquiry resources with relationships
        $inquiryResources = InquiryResource::with(['inquiry.client', 'resource', 'addedBy'])
            ->whereHas('inquiry', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Resource type breakdown
        $resourceTypeData = [];
        $resourceTypes = ['hotel', 'vehicle', 'guide', 'representative', 'extra'];
        
        foreach ($resourceTypes as $type) {
            $count = $inquiryResources->where('resource_type', $type)->count();
            $resourceTypeData[$type] = [
                'label' => ucfirst($type),
                'count' => $count,
                'percentage' => $inquiryResources->count() > 0 ? round(($count / $inquiryResources->count()) * 100, 2) : 0,
            ];
        }

        // Top resources by usage
        $topResources = $inquiryResources->groupBy('resource_id')
            ->map(function($group) {
                $resource = $group->first()->resource;
                return [
                    'resource' => $resource,
                    'resource_type' => $group->first()->resource_type,
                    'count' => $group->count(),
                    'resource_name' => $resource ? $resource->name : 'Unknown Resource'
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        // Staff performance (who adds most resources)
        $staffPerformance = $inquiryResources->groupBy('added_by')
            ->map(function($group) {
                $user = $group->first()->addedBy;
                return [
                    'user' => $user,
                    'count' => $group->count(),
                    'user_name' => $user ? $user->name : 'Unknown User'
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        // Monthly trend
        $monthlyData = $this->getMonthlyData(InquiryResource::class, $startDate, $endDate);

        // Conversion analysis (inquiries with vs without resources)
        $inquiriesWithResources = Inquiry::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('resources')
            ->count();
            
        $totalInquiries = Inquiry::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $resourceAssignmentRate = $totalInquiries > 0 ? round(($inquiriesWithResources / $totalInquiries) * 100, 2) : 0;

        return view('dashboard.reports.inquiry-resources', compact(
            'inquiryResources',
            'resourceTypeData',
            'topResources',
            'staffPerformance',
            'monthlyData',
            'resourceAssignmentRate',
            'inquiriesWithResources',
            'totalInquiries',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export report data
     */
    public function export(Request $request, $type)
    {
        $this->authorize('reports.export');
        
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        switch ($type) {
            case 'inquiries':
                return $this->exportInquiries($startDate, $endDate);
            case 'bookings':
                return $this->exportBookings($startDate, $endDate);
            case 'finance':
                return $this->exportFinance($startDate, $endDate);
            case 'operational':
                return $this->exportOperational($startDate, $endDate);
            case 'performance':
                return $this->exportPerformance($startDate, $endDate);
            default:
                abort(404, 'Report type not found');
        }
    }

    // Helper methods

    private function getMonthlyData($model, $startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            
            $count = $model::whereBetween('created_at', [$monthStart, $monthEnd])->count();
            $data[] = [
                'month' => $current->format('M Y'),
                'count' => $count,
            ];
            
            $current->addMonth();
        }
        
        return $data;
    }

    private function calculateConversionRate($inquiries)
    {
        $total = $inquiries->count();
        $converted = $inquiries->where('status', InquiryStatus::CONFIRMED)->count();
        
        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    private function getRevenueData($bookings)
    {
        return [
            'total_revenue' => $bookings->sum('total_amount'),
            'average_booking_value' => $bookings->count() > 0 ? round($bookings->sum('total_amount') / $bookings->count(), 2) : 0,
            'paid_amount' => $bookings->sum('total_paid'),
            'outstanding_amount' => $bookings->sum('remaining_amount'),
        ];
    }

    private function getGatewayData($payments)
    {
        $gateways = $payments->groupBy('gateway');
        $data = [];
        
        foreach ($gateways as $gateway => $gatewayPayments) {
            $data[] = [
                'gateway' => ucfirst(str_replace('_', ' ', $gateway)),
                'count' => $gatewayPayments->count(),
                'amount' => $gatewayPayments->sum('amount'),
                'percentage' => $payments->count() > 0 ? round(($gatewayPayments->count() / $payments->count()) * 100, 2) : 0,
            ];
        }
        
        return $data;
    }

    private function getRevenueTrend($startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dayEnd = $current->copy()->endOfDay();
            $revenue = Payment::where('status', PaymentStatus::PAID)
                ->whereBetween('paid_at', [$current, $dayEnd])
                ->sum('amount');
                
            $data[] = [
                'date' => $current->format('M d'),
                'revenue' => $revenue,
            ];
            
            $current->addDay();
        }
        
        return $data;
    }

    private function getResourceUtilization($resourceType, $startDate, $endDate)
    {
        $resourceClass = match ($resourceType) {
            'hotel' => Hotel::class,
            'vehicle' => Vehicle::class,
            'guide' => Guide::class,
            'representative' => Representative::class,
            default => throw new \InvalidArgumentException("Invalid resource type: {$resourceType}")
        };

        $resources = $resourceClass::all();
        $utilizationData = [];

        foreach ($resources as $resource) {
            $bookings = ResourceBooking::where('resource_type', $resourceType)
                ->where('resource_id', $resource->id)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                })
                ->get();

            $totalDays = $startDate->diffInDays($endDate) + 1;
            $bookedDays = $bookings->sum(function ($booking) use ($startDate, $endDate) {
                $bookingStart = max($booking->start_date, $startDate);
                $bookingEnd = min($booking->end_date, $endDate);
                return $bookingStart->diffInDays($bookingEnd) + 1;
            });

            $utilizationData[] = [
                'resource' => $resource,
                'utilization_percentage' => $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0,
                'total_days' => $totalDays,
                'booked_days' => $bookedDays,
                'bookings_count' => $bookings->count(),
                'total_revenue' => $bookings->sum('total_price'),
            ];
        }

        return collect($utilizationData);
    }

    private function getStaffPerformance($startDate, $endDate)
    {
        $staff = User::with('roles')->get();
        $performance = [];

        foreach ($staff as $user) {
            $inquiries = Inquiry::where('assigned_to', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            $performance[] = [
                'user' => $user,
                'inquiries_handled' => $inquiries->count(),
                'inquiries_confirmed' => $inquiries->where('status', InquiryStatus::CONFIRMED)->count(),
                'conversion_rate' => $inquiries->count() > 0 ? 
                    round(($inquiries->where('status', InquiryStatus::CONFIRMED)->count() / $inquiries->count()) * 100, 2) : 0,
            ];
        }

        return collect($performance)->sortByDesc('inquiries_handled');
    }

    private function calculateKPIs($startDate, $endDate)
    {
        $inquiries = Inquiry::whereBetween('created_at', [$startDate, $endDate])->get();
        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate])->get();
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])->get();

        return [
            'inquiry_to_booking_conversion' => $inquiries->count() > 0 ? 
                round(($bookings->count() / $inquiries->count()) * 100, 2) : 0,
            'booking_to_payment_conversion' => $bookings->count() > 0 ? 
                round(($payments->count() / $bookings->count()) * 100, 2) : 0,
            'average_inquiry_value' => $inquiries->count() > 0 ? 
                round($inquiries->avg('estimated_budget') ?? 0, 2) : 0,
            'average_booking_value' => $bookings->count() > 0 ? 
                round($bookings->avg('total_amount'), 2) : 0,
            'revenue_per_inquiry' => $inquiries->count() > 0 ? 
                round($payments->where('status', PaymentStatus::PAID)->sum('amount') / $inquiries->count(), 2) : 0,
        ];
    }

    private function getTrendAnalysis($startDate, $endDate)
    {
        // This would typically include more complex trend analysis
        // For now, we'll return basic month-over-month data
        return [
            'inquiries_trend' => $this->getMonthlyData(Inquiry::class, $startDate, $endDate),
            'bookings_trend' => $this->getMonthlyData(BookingFile::class, $startDate, $endDate),
            'revenue_trend' => $this->getRevenueTrend($startDate, $endDate),
        ];
    }

    private function getTopPerformers($startDate, $endDate)
    {
        // Top clients by revenue
        $topClients = Client::with(['inquiries.bookingFile.payments'])
            ->get()
            ->map(function ($client) use ($startDate, $endDate) {
                $revenue = $client->inquiries
                    ->flatMap->bookingFile
                    ->flatMap->payments
                    ->where('status', PaymentStatus::PAID)
                    ->whereBetween('paid_at', [$startDate, $endDate])
                    ->sum('amount');
                
                return [
                    'client' => $client->load('inquiries'), // Ensure inquiries are loaded
                    'revenue' => $revenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(10);

        return [
            'top_clients' => $topClients,
        ];
    }

    private function getConversionFunnel($startDate, $endDate)
    {
        $inquiries = Inquiry::whereBetween('created_at', [$startDate, $endDate])->count();
        $confirmed = Inquiry::where('status', InquiryStatus::CONFIRMED)
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate])->count();
        $payments = Payment::where('status', PaymentStatus::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        return [
            ['stage' => 'Inquiries', 'count' => $inquiries, 'percentage' => 100],
            ['stage' => 'Confirmed', 'count' => $confirmed, 'percentage' => $inquiries > 0 ? round(($confirmed / $inquiries) * 100, 2) : 0],
            ['stage' => 'Bookings', 'count' => $bookings, 'percentage' => $inquiries > 0 ? round(($bookings / $inquiries) * 100, 2) : 0],
            ['stage' => 'Payments', 'count' => $payments, 'percentage' => $inquiries > 0 ? round(($payments / $inquiries) * 100, 2) : 0],
        ];
    }

    // Export methods
    private function exportInquiries($startDate, $endDate)
    {
        $inquiries = Inquiry::with(['client'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $csv = "ID,Name,Email,Phone,Subject,Status,Created At,Client\n";
        foreach ($inquiries as $inquiry) {
            $csv .= "{$inquiry->id},{$inquiry->name},{$inquiry->email},{$inquiry->phone},{$inquiry->subject},{$inquiry->status->value},{$inquiry->created_at->format('Y-m-d H:i:s')}," . ($inquiry->client?->name ?? 'N/A') . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inquiries_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"');
    }

    private function exportBookings($startDate, $endDate)
    {
        $bookings = BookingFile::with(['inquiry.client'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $csv = "ID,File Name,Client,Status,Total Amount,Currency,Created At\n";
        foreach ($bookings as $booking) {
            $csv .= "{$booking->id},{$booking->file_name}," . ($booking->inquiry?->client?->name ?? 'N/A') . ",{$booking->status->value},{$booking->total_amount},{$booking->currency},{$booking->created_at->format('Y-m-d H:i:s')}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="bookings_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"');
    }

    private function exportFinance($startDate, $endDate)
    {
        $payments = Payment::with(['booking.inquiry.client'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $csv = "ID,Reference,Client,Amount,Status,Gateway,Paid At,Created At\n";
        foreach ($payments as $payment) {
            $csv .= "{$payment->id},{$payment->reference_number}," . ($payment->booking?->inquiry?->client?->name ?? 'N/A') . ",{$payment->amount},{$payment->status->value},{$payment->gateway},{$payment->paid_at?->format('Y-m-d H:i:s')},{$payment->created_at->format('Y-m-d H:i:s')}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="finance_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"');
    }

    private function exportOperational($startDate, $endDate)
    {
        // Get resource utilization data
        $hotelUtilization = $this->getResourceUtilization('hotel', $startDate, $endDate);
        $vehicleUtilization = $this->getResourceUtilization('vehicle', $startDate, $endDate);
        $guideUtilization = $this->getResourceUtilization('guide', $startDate, $endDate);
        $representativeUtilization = $this->getResourceUtilization('representative', $startDate, $endDate);

        // Get staff performance data
        $staffPerformance = $this->getStaffPerformance($startDate, $endDate);

        // Get resource bookings
        $resourceBookings = ResourceBooking::with(['bookingFile.inquiry.client', 'hotel', 'vehicle', 'guide', 'representative'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->orderBy('start_date')
            ->get();

        $csv = "Operational Report - {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";
        
        // Resource Utilization Summary
        $csv .= "RESOURCE UTILIZATION SUMMARY\n";
        $csv .= "Resource Type,Total Resources,Avg Utilization %,Total Bookings,Total Revenue\n";
        
        $csv .= "Hotels," . $hotelUtilization->count() . "," . $hotelUtilization->avg('utilization_percentage') . "," . $hotelUtilization->sum('bookings_count') . "," . $hotelUtilization->sum('total_revenue') . "\n";
        $csv .= "Vehicles," . $vehicleUtilization->count() . "," . $vehicleUtilization->avg('utilization_percentage') . "," . $vehicleUtilization->sum('bookings_count') . "," . $vehicleUtilization->sum('total_revenue') . "\n";
        $csv .= "Guides," . $guideUtilization->count() . "," . $guideUtilization->avg('utilization_percentage') . "," . $guideUtilization->sum('bookings_count') . "," . $guideUtilization->sum('total_revenue') . "\n";
        $csv .= "Representatives," . $representativeUtilization->count() . "," . $representativeUtilization->avg('utilization_percentage') . "," . $representativeUtilization->sum('bookings_count') . "," . $representativeUtilization->sum('total_revenue') . "\n\n";

        // Hotel Utilization Details
        $csv .= "HOTEL UTILIZATION DETAILS\n";
        $csv .= "Hotel Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue\n";
        foreach ($hotelUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Vehicle Utilization Details
        $csv .= "VEHICLE UTILIZATION DETAILS\n";
        $csv .= "Vehicle Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue\n";
        foreach ($vehicleUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Guide Utilization Details
        $csv .= "GUIDE UTILIZATION DETAILS\n";
        $csv .= "Guide Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue\n";
        foreach ($guideUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Representative Utilization Details
        $csv .= "REPRESENTATIVE UTILIZATION DETAILS\n";
        $csv .= "Representative Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Revenue\n";
        foreach ($representativeUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Staff Performance
        $csv .= "STAFF PERFORMANCE\n";
        $csv .= "Staff Name,Role,Inquiries Handled,Inquiries Confirmed,Conversion Rate\n";
        foreach ($staffPerformance as $staff) {
            $role = $staff['user']->roles->first()?->name ?? 'No Role';
            $csv .= "{$staff['user']->name},{$role},{$staff['inquiries_handled']},{$staff['inquiries_confirmed']},{$staff['conversion_rate']}%\n";
        }
        $csv .= "\n";

        // Resource Bookings
        $csv .= "RESOURCE BOOKINGS\n";
        $csv .= "Booking ID,Resource Type,Resource Name,Client,Start Date,End Date,Total Price,Status\n";
        foreach ($resourceBookings as $booking) {
            $resourceName = '';
            if ($booking->hotel) $resourceName = $booking->hotel->name;
            elseif ($booking->vehicle) $resourceName = $booking->vehicle->name;
            elseif ($booking->guide) $resourceName = $booking->guide->name;
            elseif ($booking->representative) $resourceName = $booking->representative->name;

            $clientName = $booking->bookingFile?->inquiry?->client?->name ?? 'N/A';
            
            $csv .= "{$booking->id},{$booking->resource_type},{$resourceName},{$clientName},{$booking->start_date->format('Y-m-d')},{$booking->end_date->format('Y-m-d')},{$booking->total_price},{$booking->status}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="operational_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"');
    }

    private function exportPerformance($startDate, $endDate)
    {
        // Get KPI calculations
        $kpis = $this->calculateKPIs($startDate, $endDate);
        
        // Get trend analysis
        $trends = $this->getTrendAnalysis($startDate, $endDate);
        
        // Get top performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate);
        
        // Get conversion funnel
        $conversionFunnel = $this->getConversionFunnel($startDate, $endDate);

        $csv = "Performance Report - {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}\n\n";
        
        // KPI Summary
        $csv .= "KEY PERFORMANCE INDICATORS\n";
        $csv .= "Metric,Value\n";
        $csv .= "Inquiry to Booking Conversion,{$kpis['inquiry_to_booking_conversion']}%\n";
        $csv .= "Booking to Payment Conversion,{$kpis['booking_to_payment_conversion']}%\n";
        $csv .= "Average Inquiry Value,{$kpis['average_inquiry_value']}\n";
        $csv .= "Average Booking Value,{$kpis['average_booking_value']}\n";
        $csv .= "Revenue per Inquiry,{$kpis['revenue_per_inquiry']}\n\n";

        // Trend Analysis
        $csv .= "TREND ANALYSIS\n";
        $csv .= "Period,Inquiries,Bookings,Revenue\n";
        
        // Combine monthly data for inquiries and bookings
        $inquiriesTrend = $trends['inquiries_trend'] ?? [];
        $bookingsTrend = $trends['bookings_trend'] ?? [];
        $revenueTrend = $trends['revenue_trend'] ?? [];
        
        $maxPeriods = max(count($inquiriesTrend), count($bookingsTrend), count($revenueTrend));
        for ($i = 0; $i < $maxPeriods; $i++) {
            $period = $inquiriesTrend[$i]['month'] ?? $bookingsTrend[$i]['month'] ?? 'N/A';
            $inquiries = $inquiriesTrend[$i]['count'] ?? 0;
            $bookings = $bookingsTrend[$i]['count'] ?? 0;
            $revenue = $revenueTrend[$i]['revenue'] ?? 0;
            $csv .= "{$period},{$inquiries},{$bookings},{$revenue}\n";
        }
        $csv .= "\n";

        // Top Performers (Clients)
        $csv .= "TOP PERFORMERS (CLIENTS)\n";
        $csv .= "Client Name,Total Revenue\n";
        $topClients = $topPerformers['top_clients'] ?? collect();
        foreach ($topClients as $performer) {
            $csv .= "{$performer['client']->name},{$performer['revenue']}\n";
        }
        $csv .= "\n";

        // Conversion Funnel
        $csv .= "CONVERSION FUNNEL\n";
        $csv .= "Stage,Count,Percentage\n";
        foreach ($conversionFunnel as $stage) {
            $csv .= "{$stage['stage']},{$stage['count']},{$stage['percentage']}%\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="performance_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.csv"');
    }
}
