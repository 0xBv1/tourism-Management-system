<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\BookingFile;
use App\Models\Payment;
use App\Models\Client;
use App\Models\ResourceBooking;
use App\Enums\InquiryStatus;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate comprehensive dashboard data
     */
    public function getDashboardData(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        return [
            'inquiries' => $this->getInquiryStatistics($startDate, $endDate),
            'bookings' => $this->getBookingStatistics($startDate, $endDate),
            'payments' => $this->getPaymentStatistics($startDate, $endDate),
            'clients' => $this->getClientStatistics($startDate, $endDate),
            'conversion_rates' => $this->getConversionRates($startDate, $endDate),
            'revenue_trends' => $this->getRevenueTrends($startDate, $endDate),
        ];
    }

    /**
     * Get inquiry statistics
     */
    public function getInquiryStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $inquiries = Inquiry::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => $inquiries->count(),
            'pending' => $inquiries->where('status', InquiryStatus::PENDING)->count(),
            'confirmed' => $inquiries->where('status', InquiryStatus::CONFIRMED)->count(),
            'cancelled' => $inquiries->where('status', InquiryStatus::CANCELLED)->count(),
            'completed' => $inquiries->where('status', InquiryStatus::COMPLETED)->count(),
            'conversion_rate' => $this->calculateConversionRate($inquiries->get()),
        ];
    }

    /**
     * Get booking statistics
     */
    public function getBookingStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => $bookings->count(),
            'pending' => $bookings->where('status', BookingStatus::PENDING)->count(),
            'confirmed' => $bookings->where('status', BookingStatus::CONFIRMED)->count(),
            'in_progress' => $bookings->where('status', BookingStatus::IN_PROGRESS)->count(),
            'completed' => $bookings->where('status', BookingStatus::COMPLETED)->count(),
            'cancelled' => $bookings->where('status', BookingStatus::CANCELLED)->count(),
            'total_revenue' => $bookings->sum('total_amount'),
            'average_booking_value' => $bookings->avg('total_amount'),
        ];
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => $payments->count(),
            'paid' => $payments->where('status', PaymentStatus::PAID)->count(),
            'pending' => $payments->where('status', PaymentStatus::PENDING)->count(),
            'not_paid' => $payments->where('status', PaymentStatus::NOT_PAID)->count(),
            'total_amount' => $payments->sum('amount'),
            'paid_amount' => $payments->where('status', PaymentStatus::PAID)->sum('amount'),
            'pending_amount' => $payments->where('status', PaymentStatus::PENDING)->sum('amount'),
            'not_paid_amount' => $payments->where('status', PaymentStatus::NOT_PAID)->sum('amount'),
        ];
    }

    /**
     * Get client statistics
     */
    public function getClientStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $clients = Client::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => $clients->count(),
            'new_clients' => $clients->count(),
            'active_clients' => Client::whereHas('inquiries', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->count(),
        ];
    }

    /**
     * Get conversion rates
     */
    public function getConversionRates(Carbon $startDate, Carbon $endDate): array
    {
        $inquiries = Inquiry::whereBetween('created_at', [$startDate, $endDate])->count();
        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate])->count();
        $payments = Payment::where('status', PaymentStatus::PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return [
            'inquiry_to_booking' => $inquiries > 0 ? round(($bookings / $inquiries) * 100, 2) : 0,
            'booking_to_payment' => $bookings > 0 ? round(($payments / $bookings) * 100, 2) : 0,
            'inquiry_to_payment' => $inquiries > 0 ? round(($payments / $inquiries) * 100, 2) : 0,
        ];
    }

    /**
     * Get revenue trends
     */
    public function getRevenueTrends(Carbon $startDate, Carbon $endDate): array
    {
        $trends = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dayEnd = $current->copy()->endOfDay();
            $revenue = Payment::where('status', PaymentStatus::PAID)
                ->whereBetween('paid_at', [$current, $dayEnd])
                ->sum('amount');

            $trends[] = [
                'date' => $current->format('Y-m-d'),
                'revenue' => $revenue,
            ];

            $current->addDay();
        }

        return $trends;
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate($inquiries): float
    {
        $total = $inquiries->count();
        $converted = $inquiries->where('status', InquiryStatus::CONFIRMED)->count();

        return $total > 0 ? round(($converted / $total) * 100, 2) : 0;
    }

    /**
     * Get monthly performance data
     */
    public function getMonthlyPerformanceData(int $months = 12): array
    {
        $data = [];
        $current = now()->startOfMonth();

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $current->copy()->subMonths($i)->startOfMonth();
            $monthEnd = $current->copy()->subMonths($i)->endOfMonth();

            $data[] = [
                'month' => $monthStart->format('M Y'),
                'inquiries' => $this->getInquiryStatistics($monthStart, $monthEnd),
                'bookings' => $this->getBookingStatistics($monthStart, $monthEnd),
                'payments' => $this->getPaymentStatistics($monthStart, $monthEnd),
            ];
        }

        return array_reverse($data);
    }

    /**
     * Get top performing clients
     */
    public function getTopPerformingClients(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Client::withCount('inquiries')
            ->withSum('inquiries.bookingFile', 'total_amount')
            ->having('inquiries_sum_total_amount', '>', 0)
            ->orderBy('inquiries_sum_total_amount', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get resource utilization report
     */
    public function getResourceUtilizationReport(Carbon $startDate, Carbon $endDate): array
    {
        $resourceBookings = ResourceBooking::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->orWhere(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $endDate);
            })
            ->get();

        $utilization = [
            'hotels' => $this->calculateResourceUtilization($resourceBookings->where('resource_type', 'hotel')),
            'vehicles' => $this->calculateResourceUtilization($resourceBookings->where('resource_type', 'vehicle')),
            'guides' => $this->calculateResourceUtilization($resourceBookings->where('resource_type', 'guide')),
            'representatives' => $this->calculateResourceUtilization($resourceBookings->where('resource_type', 'representative')),
        ];

        return $utilization;
    }

    /**
     * Calculate resource utilization
     */
    private function calculateResourceUtilization($bookings): array
    {
        $totalDays = $bookings->sum(function ($booking) {
            return $booking->start_date->diffInDays($booking->end_date) + 1;
        });

        $totalRevenue = $bookings->sum('total_price');

        return [
            'total_bookings' => $bookings->count(),
            'total_days' => $totalDays,
            'total_revenue' => $totalRevenue,
            'average_daily_rate' => $totalDays > 0 ? round($totalRevenue / $totalDays, 2) : 0,
        ];
    }

    /**
     * Generate export data for reports
     */
    public function generateExportData(string $type, Carbon $startDate, Carbon $endDate): array
    {
        switch ($type) {
            case 'inquiries':
                return Inquiry::with('client')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->map(function ($inquiry) {
                        return [
                            'id' => $inquiry->id,
                            'name' => $inquiry->name,
                            'email' => $inquiry->email,
                            'phone' => $inquiry->phone,
                            'subject' => $inquiry->subject,
                            'status' => $inquiry->status->getLabel(),
                            'client_name' => $inquiry->client->name ?? 'N/A',
                            'created_at' => $inquiry->created_at->format('Y-m-d H:i:s'),
                        ];
                    })->toArray();

            case 'bookings':
                return BookingFile::with(['inquiry.client'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->map(function ($booking) {
                        return [
                            'id' => $booking->id,
                            'file_name' => $booking->file_name,
                            'client_name' => $booking->inquiry->client->name ?? 'N/A',
                            'status' => $booking->status->getLabel(),
                            'total_amount' => $booking->total_amount,
                            'currency' => $booking->currency,
                            'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                        ];
                    })->toArray();

            case 'payments':
                return Payment::with(['booking.inquiry.client'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get()
                    ->map(function ($payment) {
                        return [
                            'id' => $payment->id,
                            'reference_number' => $payment->reference_number ?? 'N/A',
                            'client_name' => $payment->booking->inquiry->client->name ?? 'N/A',
                            'amount' => $payment->amount,
                            'status' => $payment->status->getLabel(),
                            'gateway' => $payment->gateway,
                            'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                            'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                        ];
                    })->toArray();

            default:
                return [];
        }
    }
}
