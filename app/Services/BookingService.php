<?php

namespace App\Services;

use App\Models\BookingFile;
use App\Models\Inquiry;
use App\Models\Client;
use App\Enums\BookingStatus;
use App\Enums\InquiryStatus;
use App\Mail\Client\BookingConfirmationMail;
use App\Notifications\Client\BookingConfirmedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class BookingService
{
    /**
     * Create a booking file from an inquiry
     */
    public function createBookingFromInquiry(Inquiry $inquiry, array $data = []): BookingFile
    {
        $bookingData = array_merge([
            'inquiry_id' => $inquiry->id,
            'file_name' => $this->generateFileName($inquiry),
            'status' => BookingStatus::PENDING,
            'total_amount' => $data['total_amount'] ?? 0,
            'currency' => $data['currency'] ?? 'USD',
            'notes' => $data['notes'] ?? null,
        ], $data);

        $bookingFile = BookingFile::create($bookingData);

        // Update inquiry status
        $inquiry->update(['status' => InquiryStatus::CONFIRMED]);

        // Send confirmation email
        if ($inquiry->client) {
            Mail::to($inquiry->client->email)->send(new BookingConfirmationMail($bookingFile));
            $inquiry->client->notify(new BookingConfirmedNotification($bookingFile));
        }

        return $bookingFile;
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(BookingFile $bookingFile, BookingStatus $status, array $data = []): bool
    {
        $updateData = array_merge(['status' => $status], $data);
        
        $result = $bookingFile->update($updateData);

        if ($result && $status === BookingStatus::CONFIRMED) {
            $this->sendBookingConfirmation($bookingFile);
        }

        return $result;
    }

    /**
     * Generate unique file name
     */
    public function generateFileName(Inquiry $inquiry): string
    {
        $clientName = $inquiry->client ? 
            str_replace(' ', '_', $inquiry->client->name) : 
            'Client';
        
        $date = now()->format('Y-m-d');
        $inquiryId = $inquiry->id;
        
        return "Booking_{$clientName}_{$date}_{$inquiryId}.pdf";
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation(BookingFile $bookingFile): void
    {
        if ($bookingFile->inquiry->client) {
            Mail::to($bookingFile->inquiry->client->email)
                ->send(new BookingConfirmationMail($bookingFile));
        }
    }

    /**
     * Get booking statistics
     */
    public function getBookingStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_bookings' => $bookings->count(),
            'pending_bookings' => $bookings->where('status', BookingStatus::PENDING)->count(),
            'confirmed_bookings' => $bookings->where('status', BookingStatus::CONFIRMED)->count(),
            'completed_bookings' => $bookings->where('status', BookingStatus::COMPLETED)->count(),
            'cancelled_bookings' => $bookings->where('status', BookingStatus::CANCELLED)->count(),
            'total_revenue' => $bookings->sum('total_amount'),
            'average_booking_value' => $bookings->avg('total_amount'),
        ];
    }

    /**
     * Get bookings by status
     */
    public function getBookingsByStatus(BookingStatus $status, int $limit = 10)
    {
        return BookingFile::where('status', $status)
            ->with(['inquiry.client'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue bookings
     */
    public function getOverdueBookings(int $daysOverdue = 30)
    {
        $cutoffDate = now()->subDays($daysOverdue);

        return BookingFile::where('status', '!=', BookingStatus::COMPLETED)
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->where('created_at', '<', $cutoffDate)
            ->with(['inquiry.client'])
            ->get();
    }

    /**
     * Complete booking
     */
    public function completeBooking(BookingFile $bookingFile, array $data = []): bool
    {
        $updateData = array_merge([
            'status' => BookingStatus::COMPLETED,
            'completed_at' => now(),
        ], $data);

        return $bookingFile->update($updateData);
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(BookingFile $bookingFile, string $reason = null): bool
    {
        $updateData = [
            'status' => BookingStatus::CANCELLED,
            'cancelled_at' => now(),
        ];

        if ($reason) {
            $updateData['cancellation_reason'] = $reason;
        }

        return $bookingFile->update($updateData);
    }

    /**
     * Get booking revenue by period
     */
    public function getRevenueByPeriod(Carbon $startDate, Carbon $endDate, string $groupBy = 'day'): array
    {
        $bookings = BookingFile::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', BookingStatus::CANCELLED);

        switch ($groupBy) {
            case 'hour':
                return $bookings->selectRaw('HOUR(created_at) as period, SUM(total_amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'day':
                return $bookings->selectRaw('DATE(created_at) as period, SUM(total_amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'week':
                return $bookings->selectRaw('WEEK(created_at) as period, SUM(total_amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'month':
                return $bookings->selectRaw('MONTH(created_at) as period, SUM(total_amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            default:
                return [];
        }
    }

    /**
     * Get top clients by booking value
     */
    public function getTopClientsByBookingValue(int $limit = 10)
    {
        return Client::withCount('inquiries')
            ->withSum('inquiries.bookingFile', 'total_amount')
            ->having('inquiries_sum_total_amount', '>', 0)
            ->orderBy('inquiries_sum_total_amount', 'desc')
            ->limit($limit)
            ->get();
    }
}
