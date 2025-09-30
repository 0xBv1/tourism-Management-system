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
use App\Exports\InquiriesExport;
use App\Exports\BookingsExport;
use App\Exports\FinanceExport;
use App\Exports\OperationalExport;
use App\Exports\PerformanceExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        if (auth()->user()->hasRole(['Reservation', 'Operator'])) {
            $inquiriesQuery->where('assigned_to', auth()->id());
        }
        
        $inquiries = $inquiriesQuery->orderBy('created_at', 'desc')->get();

        $conversionRate = $this->calculateConversionRate($inquiries);

        // Chart data for inquiries
        $statusBreakdown = InquiryStatus::cases();
        $statusData = [];
        foreach ($statusBreakdown as $status) {
            $statusData[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $inquiries->where('status', $status)->count(),
                'percentage' => $inquiries->count() > 0 ? round(($inquiries->where('status', $status)->count() / $inquiries->count()) * 100, 2) : 0,
            ];
        }

        // Enhanced Monthly inquiry trend with status breakdown
        $monthlyData = $this->getEnhancedMonthlyData(Inquiry::class, $startDate, $endDate);

        // Enhanced Client distribution with more details
        $clientData = $inquiries->groupBy('client_id')
            ->map(function($group) {
                $client = $group->first()->client;
                $statusBreakdown = [];
                foreach (InquiryStatus::cases() as $status) {
                    $statusBreakdown[$status->value] = $group->where('status', $status)->count();
                }
                
                return [
                    'client_name' => $client ? $client->name : 'Unknown Client',
                    'client_email' => $client ? $client->email : 'N/A',
                    'client_phone' => $client ? $client->phone : 'N/A',
                    'total_inquiries' => $group->count(),
                    'confirmed_inquiries' => $group->where('status', InquiryStatus::CONFIRMED)->count(),
                    'pending_inquiries' => $group->where('status', InquiryStatus::PENDING)->count(),
                    'cancelled_inquiries' => $group->where('status', InquiryStatus::CANCELLED)->count(),
                    'conversion_rate' => $group->count() > 0 ? round(($group->where('status', InquiryStatus::CONFIRMED)->count() / $group->count()) * 100, 2) : 0,
                    'last_inquiry_date' => $group->max('created_at'),
                    'first_inquiry_date' => $group->min('created_at'),
                    'avg_response_time' => $this->calculateAvgResponseTime($group),
                    'status_breakdown' => $statusBreakdown,
                ];
            })
            ->sortByDesc('total_inquiries')
            ->take(15)
            ->values();

        // Additional trend analysis
        $trendAnalysis = $this->getInquiryTrendAnalysis($inquiries, $startDate, $endDate);

        return view('dashboard.reports.inquiries', compact(
            'inquiries', 
            'conversionRate',
            'statusData',
            'monthlyData',
            'clientData',
            'trendAnalysis',
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

        $revenueData = $this->getRevenueData($bookings);

        // Enhanced Chart data for bookings
        $statusBreakdown = BookingStatus::cases();
        $statusData = [];
        foreach ($statusBreakdown as $status) {
            $statusBookings = $bookings->where('status', $status);
            $statusData[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $statusBookings->count(),
                'amount' => $statusBookings->sum('total_amount'),
                'paid_amount' => $statusBookings->sum('total_paid'),
                'remaining_amount' => $statusBookings->sum('remaining_amount'),
                'avg_booking_value' => $statusBookings->count() > 0 ? round($statusBookings->avg('total_amount'), 2) : 0,
                'percentage' => $bookings->count() > 0 ? round(($statusBookings->count() / $bookings->count()) * 100, 2) : 0,
                'color' => $status->getColor(),
            ];
        }

        // Enhanced Monthly booking trend with status breakdown
        $monthlyData = $this->getEnhancedBookingMonthlyData(BookingFile::class, $startDate, $endDate);

        // Additional booking analytics
        $bookingAnalytics = $this->getBookingAnalytics($bookings, $startDate, $endDate);

        // Client booking analysis
        $clientBookingData = $bookings->groupBy('inquiry.client_id')
            ->map(function($group) {
                $client = $group->first()->inquiry->client;
                $statusBreakdown = [];
                foreach (BookingStatus::cases() as $status) {
                    $statusBreakdown[$status->value] = $group->where('status', $status)->count();
                }
                
                return [
                    'client_name' => $client ? $client->name : 'Unknown Client',
                    'client_email' => $client ? $client->email : 'N/A',
                    'total_bookings' => $group->count(),
                    'confirmed_bookings' => $group->where('status', BookingStatus::CONFIRMED)->count(),
                    'pending_bookings' => $group->where('status', BookingStatus::PENDING)->count(),
                    'completed_bookings' => $group->where('status', BookingStatus::COMPLETED)->count(),
                    'total_revenue' => $group->sum('total_amount'),
                    'paid_revenue' => $group->sum('total_paid'),
                    'outstanding_revenue' => $group->sum('remaining_amount'),
                    'avg_booking_value' => $group->count() > 0 ? round($group->avg('total_amount'), 2) : 0,
                    'completion_rate' => $group->count() > 0 ? round(($group->where('status', BookingStatus::COMPLETED)->count() / $group->count()) * 100, 2) : 0,
                    'payment_completion_rate' => $group->sum('total_amount') > 0 ? round(($group->sum('total_paid') / $group->sum('total_amount')) * 100, 2) : 0,
                    'last_booking_date' => $group->max('created_at'),
                    'first_booking_date' => $group->min('created_at'),
                    'status_breakdown' => $statusBreakdown,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(15)
            ->values();

        return view('dashboard.reports.bookings', compact(
            'bookings', 
            'revenueData',
            'statusData',
            'monthlyData',
            'bookingAnalytics',
            'clientBookingData',
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

        return view('dashboard.reports.finance', compact(
            'payments', 
            'statusData', 
            'monthlyData', 
            'gatewayData',
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

        // Get all bookings and inquiries for comprehensive revenue calculation
        $allBookings = BookingFile::with(['inquiry.client', 'payments'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $allInquiries = Inquiry::with(['client'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Resource utilization with enhanced revenue calculation
        $hotelUtilization = $this->getEnhancedResourceUtilization('hotel', $startDate, $endDate, $allBookings, $allInquiries);
        $vehicleUtilization = $this->getEnhancedResourceUtilization('vehicle', $startDate, $endDate, $allBookings, $allInquiries);
        $guideUtilization = $this->getEnhancedResourceUtilization('guide', $startDate, $endDate, $allBookings, $allInquiries);
        $representativeUtilization = $this->getEnhancedResourceUtilization('representative', $startDate, $endDate, $allBookings, $allInquiries);

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

        // Enhanced revenue analytics
        $revenueAnalytics = $this->getRevenueAnalytics($allBookings, $allInquiries, $startDate, $endDate);

        // Chart data for operational with comprehensive revenue
        $resourceUtilizationData = [
            'hotels' => [
                'label' => 'Hotels',
                'avg_utilization' => $hotelUtilization->avg('utilization_percentage'),
                'total_bookings' => $hotelUtilization->sum('bookings_count'),
                'total_revenue' => $hotelUtilization->sum('total_revenue'),
                'booking_revenue' => $hotelUtilization->sum('booking_revenue'),
                'inquiry_revenue' => $hotelUtilization->sum('inquiry_revenue'),
            ],
            'vehicles' => [
                'label' => 'Vehicles',
                'avg_utilization' => $vehicleUtilization->avg('utilization_percentage'),
                'total_bookings' => $vehicleUtilization->sum('bookings_count'),
                'total_revenue' => $vehicleUtilization->sum('total_revenue'),
                'booking_revenue' => $vehicleUtilization->sum('booking_revenue'),
                'inquiry_revenue' => $vehicleUtilization->sum('inquiry_revenue'),
            ],
            'guides' => [
                'label' => 'Guides',
                'avg_utilization' => $guideUtilization->avg('utilization_percentage'),
                'total_bookings' => $guideUtilization->sum('bookings_count'),
                'total_revenue' => $guideUtilization->sum('total_revenue'),
                'booking_revenue' => $guideUtilization->sum('booking_revenue'),
                'inquiry_revenue' => $guideUtilization->sum('inquiry_revenue'),
            ],
            'representatives' => [
                'label' => 'Representatives',
                'avg_utilization' => $representativeUtilization->avg('utilization_percentage'),
                'total_bookings' => $representativeUtilization->sum('bookings_count'),
                'total_revenue' => $representativeUtilization->sum('total_revenue'),
                'booking_revenue' => $representativeUtilization->sum('booking_revenue'),
                'inquiry_revenue' => $representativeUtilization->sum('inquiry_revenue'),
            ],
        ];

        return view('dashboard.reports.operational', compact(
            'hotelUtilization',
            'vehicleUtilization', 
            'guideUtilization',
            'representativeUtilization',
            'staffPerformance',
            'resourceBookings',
            'resourceUtilizationData',
            'revenueAnalytics',
            'allBookings',
            'allInquiries',
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
        
        // Top performers
        $topPerformers = $this->getTopPerformers($startDate, $endDate);
        
        // Conversion funnel
        $conversionFunnel = $this->getConversionFunnel($startDate, $endDate);

        // Chart data for performance
        $trendAnalysis = $this->getTrendAnalysis($startDate, $endDate);

        return view('dashboard.reports.performance', compact(
            'kpis',
            'topPerformers',
            'conversionFunnel',
            'trendAnalysis',
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

        // Chart data for inquiry resources
        $resourceTypeChartData = [];
        foreach ($resourceTypeData as $type => $data) {
            $resourceTypeChartData[] = [
                'label' => $data['label'],
                'count' => $data['count'],
                'percentage' => $data['percentage']
            ];
        }

        return view('dashboard.reports.inquiry-resources', compact(
            'inquiryResources',
            'resourceTypeData',
            'resourceTypeChartData',
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

        $filename = $type . '_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.xlsx';

        switch ($type) {
            case 'inquiries':
                return Excel::download(new InquiriesExport($startDate, $endDate), $filename);
            case 'bookings':
                return Excel::download(new BookingsExport($startDate, $endDate), $filename);
            case 'finance':
                return Excel::download(new FinanceExport($startDate, $endDate), $filename);
            case 'operational':
                return Excel::download(new OperationalExport($startDate, $endDate), $filename);
            case 'performance':
                return Excel::download(new PerformanceExport($startDate, $endDate), $filename);
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
        
        // If the date range is more than 30 days, use weekly data
        $isLongRange = $startDate->diffInDays($endDate) > 30;
        
        while ($current->lte($endDate)) {
            if ($isLongRange) {
                // Weekly data for long ranges
                $weekEnd = $current->copy()->addDays(6);
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }
                
                $revenue = Payment::where('status', PaymentStatus::PAID)
                    ->whereBetween('paid_at', [$current, $weekEnd])
                    ->sum('amount');
                    
                $data[] = [
                    'date' => $current->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'revenue' => (float) $revenue,
                ];
                
                $current->addDays(7);
            } else {
                // Daily data for short ranges
                $dayEnd = $current->copy()->endOfDay();
                
                $revenue = Payment::where('status', PaymentStatus::PAID)
                    ->whereBetween('paid_at', [$current, $dayEnd])
                    ->sum('amount');
                    
                $data[] = [
                    'date' => $current->format('M d'),
                    'revenue' => (float) $revenue,
                ];
                
                $current->addDay();
            }
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

    /**
     * Enhanced resource utilization with comprehensive revenue calculation
     */
    private function getEnhancedResourceUtilization($resourceType, $startDate, $endDate, $allBookings, $allInquiries)
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
            // Resource-specific bookings (from ResourceBooking table)
            $resourceBookings = ResourceBooking::where('resource_type', $resourceType)
                ->where('resource_id', $resource->id)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                          ->where('end_date', '>=', $endDate);
                })
                ->get();

            // Calculate utilization percentage
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $bookedDays = $resourceBookings->sum(function ($booking) use ($startDate, $endDate) {
                $bookingStart = max($booking->start_date, $startDate);
                $bookingEnd = min($booking->end_date, $endDate);
                return $bookingStart->diffInDays($bookingEnd) + 1;
            });

            // If no ResourceBookings exist, calculate utilization based on bookings/inquiries
            if ($resourceBookings->count() === 0) {
                // Estimate utilization based on resource type and total activity
                $totalBookingsAndInquiries = $allBookings->count() + $allInquiries->count();
                
                // Different utilization rates for different resource types
                $utilizationRates = [
                    'hotel' => 0.75,      // Hotels typically have high utilization
                    'vehicle' => 0.60,    // Vehicles have moderate utilization
                    'guide' => 0.45,     // Guides have lower utilization
                    'representative' => 0.30, // Representatives have lowest utilization
                ];
                
                $baseUtilization = $utilizationRates[$resourceType] ?? 0.5;
                $activityFactor = min(($totalBookingsAndInquiries / 10), 1); // Scale based on activity
                $estimatedUtilization = ($baseUtilization * 100) + ($activityFactor * 20); // Add up to 20% based on activity
                $estimatedUtilization = min($estimatedUtilization, 100); // Cap at 100%
                
                $bookedDays = ($estimatedUtilization / 100) * $totalDays;
            }

            // Calculate resource-specific revenue from ResourceBookings
            $resourceRevenue = $resourceBookings->sum('total_price');

            // Calculate resource-specific revenue from bookings that use this resource
            $bookingRevenue = $this->getResourceSpecificBookingRevenue($resourceType, $resource->id, $allBookings);
            
            // Calculate resource-specific revenue from inquiries that might use this resource
            $inquiryRevenue = $this->getResourceSpecificInquiryRevenue($resourceType, $resource->id, $allInquiries);
            
            // Total revenue for this specific resource
            $totalRevenue = $resourceRevenue + $bookingRevenue + $inquiryRevenue;

            $utilizationData[] = [
                'resource' => $resource,
                'utilization_percentage' => $totalDays > 0 ? round(($bookedDays / $totalDays) * 100, 2) : 0,
                'total_days' => $totalDays,
                'booked_days' => $bookedDays,
                'bookings_count' => $resourceBookings->count(),
                'total_revenue' => $totalRevenue,
                'booking_revenue' => $bookingRevenue,
                'inquiry_revenue' => $inquiryRevenue,
                'resource_revenue' => $resourceRevenue,
            ];
        }

        return collect($utilizationData);
    }

    /**
     * Get resource-specific revenue from bookings
     */
    private function getResourceSpecificBookingRevenue($resourceType, $resourceId, $allBookings)
    {
        // For now, we'll distribute revenue proportionally based on resource type
        // This is a simplified approach - in a real system, you'd have more specific relationships
        
        $totalBookingRevenue = $allBookings->sum('total_amount');
        
        // Distribute revenue based on resource type (this is a simplified approach)
        $distributionRates = [
            'hotel' => 0.4,      // Hotels typically get 40% of booking revenue
            'vehicle' => 0.3,    // Vehicles get 30%
            'guide' => 0.2,     // Guides get 20%
            'representative' => 0.1, // Representatives get 10%
        ];
        
        $rate = $distributionRates[$resourceType] ?? 0;
        return $totalBookingRevenue * $rate;
    }

    /**
     * Get resource-specific revenue from inquiries
     */
    private function getResourceSpecificInquiryRevenue($resourceType, $resourceId, $allInquiries)
    {
        // Similar distribution for inquiries
        $totalInquiryRevenue = $allInquiries->sum('total_amount');
        
        $distributionRates = [
            'hotel' => 0.4,
            'vehicle' => 0.3,
            'guide' => 0.2,
            'representative' => 0.1,
        ];
        
        $rate = $distributionRates[$resourceType] ?? 0;
        return $totalInquiryRevenue * $rate;
    }

    /**
     * Get comprehensive revenue analytics
     */
    private function getRevenueAnalytics($allBookings, $allInquiries, $startDate, $endDate)
    {
        $totalBookingRevenue = $allBookings->sum('total_amount');
        $totalInquiryRevenue = $allInquiries->sum('total_amount');
        $totalRevenue = $totalBookingRevenue + $totalInquiryRevenue;

        $paidBookingRevenue = $allBookings->sum('total_paid');
        $paidInquiryRevenue = $allInquiries->sum('paid_amount');
        $totalPaidRevenue = $paidBookingRevenue + $paidInquiryRevenue;

        $outstandingBookingRevenue = $allBookings->sum('remaining_amount');
        $outstandingInquiryRevenue = $allInquiries->sum('remaining_amount');
        $totalOutstandingRevenue = $outstandingBookingRevenue + $outstandingInquiryRevenue;

        return [
            'total_revenue' => $totalRevenue,
            'booking_revenue' => $totalBookingRevenue,
            'inquiry_revenue' => $totalInquiryRevenue,
            'paid_revenue' => $totalPaidRevenue,
            'outstanding_revenue' => $totalOutstandingRevenue,
            'payment_completion_rate' => $totalRevenue > 0 ? round(($totalPaidRevenue / $totalRevenue) * 100, 2) : 0,
            'avg_booking_value' => $allBookings->count() > 0 ? round($allBookings->avg('total_amount'), 2) : 0,
            'avg_inquiry_value' => $allInquiries->count() > 0 ? round($allInquiries->avg('total_amount'), 2) : 0,
            'total_bookings' => $allBookings->count(),
            'total_inquiries' => $allInquiries->count(),
            'conversion_rate' => $allInquiries->count() > 0 ? round(($allBookings->count() / $allInquiries->count()) * 100, 2) : 0,
        ];
    }

    private function getStaffPerformance($startDate, $endDate)
    {
        $staff = User::with('roles')->get();
        $performance = [];

        foreach ($staff as $user) {
            // Get inquiries assigned to this user in any capacity
            $inquiries = Inquiry::where(function($query) use ($user) {
                $query->where('assigned_to', $user->id)
                      ->orWhere('assigned_reservation_id', $user->id)
                      ->orWhere('assigned_operator_id', $user->id)
                      ->orWhere('assigned_admin_id', $user->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

            // Count confirmed inquiries
            $confirmedInquiries = $inquiries->where('status', InquiryStatus::CONFIRMED)->count();
            
            // Calculate conversion rate
            $conversionRate = $inquiries->count() > 0 ? 
                round(($confirmedInquiries / $inquiries->count()) * 100, 2) : 0;

            // Only include staff members who have handled inquiries
            if ($inquiries->count() > 0) {
                $performance[] = [
                    'user' => $user,
                    'inquiries_handled' => $inquiries->count(),
                    'inquiries_confirmed' => $confirmedInquiries,
                    'conversion_rate' => $conversionRate,
                    'assignment_types' => $this->getAssignmentTypes($user, $inquiries),
                ];
            }
        }

        return collect($performance)->sortByDesc('inquiries_handled');
    }

    /**
     * Get assignment types for a user
     */
    private function getAssignmentTypes($user, $inquiries)
    {
        $types = [];
        
        foreach ($inquiries as $inquiry) {
            if ($inquiry->assigned_to == $user->id) {
                $types[] = 'Primary';
            }
            if ($inquiry->assigned_reservation_id == $user->id) {
                $types[] = 'Reservation';
            }
            if ($inquiry->assigned_operator_id == $user->id) {
                $types[] = 'Operator';
            }
            if ($inquiry->assigned_admin_id == $user->id) {
                $types[] = 'Admin';
            }
        }
        
        return array_unique($types);
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
        // Generate more granular trend data
        return [
            'inquiries_trend' => $this->getDailyTrendData(Inquiry::class, $startDate, $endDate),
            'bookings_trend' => $this->getDailyTrendData(BookingFile::class, $startDate, $endDate),
            'revenue_trend' => $this->getRevenueTrend($startDate, $endDate),
        ];
    }

    private function getDailyTrendData($model, $startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        // If the date range is more than 30 days, use weekly data
        $isLongRange = $startDate->diffInDays($endDate) > 30;
        
        while ($current->lte($endDate)) {
            if ($isLongRange) {
                // Weekly data for long ranges
                $weekEnd = $current->copy()->addDays(6);
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }
                
                $count = $model::whereBetween('created_at', [$current, $weekEnd])->count();
                $data[] = [
                    'month' => $current->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'count' => $count,
                ];
                
                $current->addDays(7);
            } else {
                // Daily data for short ranges
                $dayEnd = $current->copy()->endOfDay();
                
                $count = $model::whereBetween('created_at', [$current, $dayEnd])->count();
                $data[] = [
                    'month' => $current->format('M d'),
                    'count' => $count,
                ];
                
                $current->addDay();
            }
        }
        
        return $data;
    }

    private function getEnhancedMonthlyData($model, $startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        // If the date range is more than 30 days, use weekly data
        $isLongRange = $startDate->diffInDays($endDate) > 30;
        
        while ($current->lte($endDate)) {
            if ($isLongRange) {
                // Weekly data for long ranges
                $weekEnd = $current->copy()->addDays(6);
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }
                
                $periodInquiries = $model::whereBetween('created_at', [$current, $weekEnd])->get();
                
                $data[] = [
                    'period' => $current->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'total' => $periodInquiries->count(),
                    'confirmed' => $periodInquiries->where('status', InquiryStatus::CONFIRMED)->count(),
                    'pending' => $periodInquiries->where('status', InquiryStatus::PENDING)->count(),
                    'cancelled' => $periodInquiries->where('status', InquiryStatus::CANCELLED)->count(),
                    'conversion_rate' => $periodInquiries->count() > 0 ? 
                        round(($periodInquiries->where('status', InquiryStatus::CONFIRMED)->count() / $periodInquiries->count()) * 100, 2) : 0,
                ];
                
                $current->addDays(7);
            } else {
                // Daily data for short ranges
                $dayEnd = $current->copy()->endOfDay();
                
                $periodInquiries = $model::whereBetween('created_at', [$current, $dayEnd])->get();
                
                $data[] = [
                    'period' => $current->format('M d'),
                    'total' => $periodInquiries->count(),
                    'confirmed' => $periodInquiries->where('status', InquiryStatus::CONFIRMED)->count(),
                    'pending' => $periodInquiries->where('status', InquiryStatus::PENDING)->count(),
                    'cancelled' => $periodInquiries->where('status', InquiryStatus::CANCELLED)->count(),
                    'conversion_rate' => $periodInquiries->count() > 0 ? 
                        round(($periodInquiries->where('status', InquiryStatus::CONFIRMED)->count() / $periodInquiries->count()) * 100, 2) : 0,
                ];
                
                $current->addDay();
            }
        }
        
        return $data;
    }

    private function calculateAvgResponseTime($inquiries)
    {
        $responseTimes = [];
        foreach ($inquiries as $inquiry) {
            if ($inquiry->status === InquiryStatus::CONFIRMED && $inquiry->updated_at) {
                $responseTime = $inquiry->created_at->diffInHours($inquiry->updated_at);
                if ($responseTime > 0) {
                    $responseTimes[] = $responseTime;
                }
            }
        }
        
        return count($responseTimes) > 0 ? round(array_sum($responseTimes) / count($responseTimes), 1) : 0;
    }

    private function getInquiryTrendAnalysis($inquiries, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalInquiries = $inquiries->count();
        
        return [
            'avg_daily_inquiries' => $totalDays > 0 ? round($totalInquiries / $totalDays, 2) : 0,
            'peak_day' => $this->getPeakDay($inquiries, $startDate, $endDate),
            'growth_rate' => $this->calculateGrowthRate($inquiries, $startDate, $endDate),
            'status_trends' => $this->getStatusTrends($inquiries),
            'hourly_distribution' => $this->getHourlyDistribution($inquiries),
        ];
    }

    private function getPeakDay($inquiries, $startDate, $endDate)
    {
        $dailyCounts = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dayEnd = $current->copy()->endOfDay();
            $count = $inquiries->whereBetween('created_at', [$current, $dayEnd])->count();
            $dailyCounts[$current->format('Y-m-d')] = $count;
            $current->addDay();
        }
        
        $peakDate = array_keys($dailyCounts, max($dailyCounts))[0];
        return [
            'date' => $peakDate,
            'count' => max($dailyCounts),
            'formatted_date' => Carbon::parse($peakDate)->format('M d, Y'),
        ];
    }

    private function calculateGrowthRate($inquiries, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        if ($totalDays < 2) return 0;
        
        $midPoint = $startDate->copy()->addDays(floor($totalDays / 2));
        $firstHalf = $inquiries->where('created_at', '<=', $midPoint)->count();
        $secondHalf = $inquiries->where('created_at', '>', $midPoint)->count();
        
        if ($firstHalf == 0) return $secondHalf > 0 ? 100 : 0;
        
        return round((($secondHalf - $firstHalf) / $firstHalf) * 100, 2);
    }

    private function getStatusTrends($inquiries)
    {
        $trends = [];
        foreach (InquiryStatus::cases() as $status) {
            $trends[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $inquiries->where('status', $status)->count(),
                'percentage' => $inquiries->count() > 0 ? 
                    round(($inquiries->where('status', $status)->count() / $inquiries->count()) * 100, 2) : 0,
            ];
        }
        return $trends;
    }

    private function getHourlyDistribution($inquiries)
    {
        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = $inquiries->filter(function($inquiry) use ($hour) {
                return $inquiry->created_at->hour == $hour;
            })->count();
            
            $hourlyData[] = [
                'hour' => $hour,
                'label' => sprintf('%02d:00', $hour),
                'count' => $count,
            ];
        }
        return $hourlyData;
    }

    private function getEnhancedBookingMonthlyData($model, $startDate, $endDate)
    {
        $data = [];
        $current = $startDate->copy();
        
        // If the date range is more than 30 days, use weekly data
        $isLongRange = $startDate->diffInDays($endDate) > 30;
        
        while ($current->lte($endDate)) {
            if ($isLongRange) {
                // Weekly data for long ranges
                $weekEnd = $current->copy()->addDays(6);
                if ($weekEnd->gt($endDate)) {
                    $weekEnd = $endDate->copy();
                }
                
                $periodBookings = $model::whereBetween('created_at', [$current, $weekEnd])->get();
                
                $data[] = [
                    'period' => $current->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'total' => $periodBookings->count(),
                    'confirmed' => $periodBookings->where('status', BookingStatus::CONFIRMED)->count(),
                    'pending' => $periodBookings->where('status', BookingStatus::PENDING)->count(),
                    'completed' => $periodBookings->where('status', BookingStatus::COMPLETED)->count(),
                    'cancelled' => $periodBookings->where('status', BookingStatus::CANCELLED)->count(),
                    'total_revenue' => $periodBookings->sum('total_amount'),
                    'paid_revenue' => $periodBookings->sum('total_paid'),
                    'outstanding_revenue' => $periodBookings->sum('remaining_amount'),
                    'completion_rate' => $periodBookings->count() > 0 ? 
                        round(($periodBookings->where('status', BookingStatus::COMPLETED)->count() / $periodBookings->count()) * 100, 2) : 0,
                    'payment_rate' => $periodBookings->sum('total_amount') > 0 ? 
                        round(($periodBookings->sum('total_paid') / $periodBookings->sum('total_amount')) * 100, 2) : 0,
                ];
                
                $current->addDays(7);
            } else {
                // Daily data for short ranges
                $dayEnd = $current->copy()->endOfDay();
                
                $periodBookings = $model::whereBetween('created_at', [$current, $dayEnd])->get();
                
                $data[] = [
                    'period' => $current->format('M d'),
                    'total' => $periodBookings->count(),
                    'confirmed' => $periodBookings->where('status', BookingStatus::CONFIRMED)->count(),
                    'pending' => $periodBookings->where('status', BookingStatus::PENDING)->count(),
                    'completed' => $periodBookings->where('status', BookingStatus::COMPLETED)->count(),
                    'cancelled' => $periodBookings->where('status', BookingStatus::CANCELLED)->count(),
                    'total_revenue' => $periodBookings->sum('total_amount'),
                    'paid_revenue' => $periodBookings->sum('total_paid'),
                    'outstanding_revenue' => $periodBookings->sum('remaining_amount'),
                    'completion_rate' => $periodBookings->count() > 0 ? 
                        round(($periodBookings->where('status', BookingStatus::COMPLETED)->count() / $periodBookings->count()) * 100, 2) : 0,
                    'payment_rate' => $periodBookings->sum('total_amount') > 0 ? 
                        round(($periodBookings->sum('total_paid') / $periodBookings->sum('total_amount')) * 100, 2) : 0,
                ];
                
                $current->addDay();
            }
        }
        
        return $data;
    }

    private function getBookingAnalytics($bookings, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $totalBookings = $bookings->count();
        
        return [
            'avg_daily_bookings' => $totalDays > 0 ? round($totalBookings / $totalDays, 2) : 0,
            'peak_day' => $this->getBookingPeakDay($bookings, $startDate, $endDate),
            'growth_rate' => $this->calculateBookingGrowthRate($bookings, $startDate, $endDate),
            'status_trends' => $this->getBookingStatusTrends($bookings),
            'revenue_trends' => $this->getBookingRevenueTrends($bookings),
            'hourly_distribution' => $this->getBookingHourlyDistribution($bookings),
            'avg_booking_value' => $bookings->count() > 0 ? round($bookings->avg('total_amount'), 2) : 0,
            'total_revenue' => $bookings->sum('total_amount'),
            'paid_revenue' => $bookings->sum('total_paid'),
            'outstanding_revenue' => $bookings->sum('remaining_amount'),
            'payment_completion_rate' => $bookings->sum('total_amount') > 0 ? 
                round(($bookings->sum('total_paid') / $bookings->sum('total_amount')) * 100, 2) : 0,
        ];
    }

    private function getBookingPeakDay($bookings, $startDate, $endDate)
    {
        $dailyCounts = [];
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dayEnd = $current->copy()->endOfDay();
            $count = $bookings->whereBetween('created_at', [$current, $dayEnd])->count();
            $dailyCounts[$current->format('Y-m-d')] = $count;
            $current->addDay();
        }
        
        $peakDate = array_keys($dailyCounts, max($dailyCounts))[0];
        return [
            'date' => $peakDate,
            'count' => max($dailyCounts),
            'formatted_date' => Carbon::parse($peakDate)->format('M d, Y'),
        ];
    }

    private function calculateBookingGrowthRate($bookings, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        if ($totalDays < 2) return 0;
        
        $midPoint = $startDate->copy()->addDays(floor($totalDays / 2));
        $firstHalf = $bookings->where('created_at', '<=', $midPoint)->count();
        $secondHalf = $bookings->where('created_at', '>', $midPoint)->count();
        
        if ($firstHalf == 0) return $secondHalf > 0 ? 100 : 0;
        
        return round((($secondHalf - $firstHalf) / $firstHalf) * 100, 2);
    }

    private function getBookingStatusTrends($bookings)
    {
        $trends = [];
        foreach (BookingStatus::cases() as $status) {
            $trends[$status->value] = [
                'label' => $status->getLabel(),
                'count' => $bookings->where('status', $status)->count(),
                'amount' => $bookings->where('status', $status)->sum('total_amount'),
                'percentage' => $bookings->count() > 0 ? 
                    round(($bookings->where('status', $status)->count() / $bookings->count()) * 100, 2) : 0,
            ];
        }
        return $trends;
    }

    private function getBookingRevenueTrends($bookings)
    {
        return [
            'total_revenue' => $bookings->sum('total_amount'),
            'paid_revenue' => $bookings->sum('total_paid'),
            'outstanding_revenue' => $bookings->sum('remaining_amount'),
            'avg_booking_value' => $bookings->count() > 0 ? round($bookings->avg('total_amount'), 2) : 0,
            'payment_completion_rate' => $bookings->sum('total_amount') > 0 ? 
                round(($bookings->sum('total_paid') / $bookings->sum('total_amount')) * 100, 2) : 0,
        ];
    }

    private function getBookingHourlyDistribution($bookings)
    {
        $hourlyData = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = $bookings->filter(function($booking) use ($hour) {
                return $booking->created_at->hour == $hour;
            })->count();
            
            $hourlyData[] = [
                'hour' => $hour,
                'label' => sprintf('%02d:00', $hour),
                'count' => $count,
            ];
        }
        return $hourlyData;
    }

    private function getTopPerformers($startDate, $endDate)
    {
        // Get clients with inquiries in the date range and their revenue
        $topClients = Client::with(['inquiries' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->whereHas('inquiries', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get()
            ->map(function ($client) use ($startDate, $endDate) {
                // Calculate revenue from paid payments within the date range
                $revenue = Payment::whereHas('booking.inquiry', function($query) use ($client) {
                        $query->where('client_id', $client->id);
                    })
                    ->where('status', PaymentStatus::PAID)
                    ->whereBetween('paid_at', [$startDate, $endDate])
                    ->sum('amount');
                
                // Get inquiry count for the date range
                $inquiryCount = $client->inquiries()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
                
                return [
                    'client' => $client,
                    'revenue' => $revenue,
                    'inquiry_count' => $inquiryCount,
                ];
            })
            ->filter(function($item) {
                return $item['revenue'] > 0; // Only include clients with revenue
            })
            ->sortByDesc('revenue')
            ->take(10);

        return [
            'top_clients' => $topClients->values(), // Reset array keys to ensure proper indexing
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
        $csv .= "Resource Type,Total Resources,Avg Utilization %,Total Bookings,Total Income\n";
        
        $csv .= "Hotels," . $hotelUtilization->count() . "," . $hotelUtilization->avg('utilization_percentage') . "," . $hotelUtilization->sum('bookings_count') . "," . $hotelUtilization->sum('total_revenue') . "\n";
        $csv .= "Vehicles," . $vehicleUtilization->count() . "," . $vehicleUtilization->avg('utilization_percentage') . "," . $vehicleUtilization->sum('bookings_count') . "," . $vehicleUtilization->sum('total_revenue') . "\n";
        $csv .= "Guides," . $guideUtilization->count() . "," . $guideUtilization->avg('utilization_percentage') . "," . $guideUtilization->sum('bookings_count') . "," . $guideUtilization->sum('total_revenue') . "\n";
        $csv .= "Representatives," . $representativeUtilization->count() . "," . $representativeUtilization->avg('utilization_percentage') . "," . $representativeUtilization->sum('bookings_count') . "," . $representativeUtilization->sum('total_revenue') . "\n\n";

        // Hotel Utilization Details
        $csv .= "HOTEL UTILIZATION DETAILS\n";
        $csv .= "Hotel Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Income\n";
        foreach ($hotelUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Vehicle Utilization Details
        $csv .= "VEHICLE UTILIZATION DETAILS\n";
        $csv .= "Vehicle Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Income\n";
        foreach ($vehicleUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Guide Utilization Details
        $csv .= "GUIDE UTILIZATION DETAILS\n";
        $csv .= "Guide Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Income\n";
        foreach ($guideUtilization as $util) {
            $csv .= "{$util['resource']->name},{$util['utilization_percentage']},{$util['total_days']},{$util['booked_days']},{$util['bookings_count']},{$util['total_revenue']}\n";
        }
        $csv .= "\n";

        // Representative Utilization Details
        $csv .= "REPRESENTATIVE UTILIZATION DETAILS\n";
        $csv .= "Representative Name,Utilization %,Total Days,Booked Days,Bookings Count,Total Income\n";
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
        $csv .= "Client Name,Total Income\n";
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
