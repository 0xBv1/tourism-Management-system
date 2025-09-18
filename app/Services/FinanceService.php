<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\BookingFile;
use App\Models\Client;
use App\Enums\PaymentStatus;
use App\Mail\Client\PaymentReceiptMail;
use App\Mail\Client\MonthlyStatementMail;
use App\Notifications\Client\PaymentOverdueNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class FinanceService
{
    /**
     * Create a payment
     */
    public function createPayment(BookingFile $bookingFile, array $data): Payment
    {
        $paymentData = array_merge([
            'booking_id' => $bookingFile->id,
            'status' => PaymentStatus::PENDING,
            'gateway' => $data['gateway'] ?? 'bank_transfer',
        ], $data);

        $payment = Payment::create($paymentData);

        // Send payment receipt if marked as paid
        if ($payment->status === PaymentStatus::PAID) {
            $this->sendPaymentReceipt($payment);
        }

        return $payment;
    }

    /**
     * Process payment
     */
    public function processPayment(Payment $payment, array $data = []): bool
    {
        $updateData = array_merge([
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ], $data);

        $result = $payment->update($updateData);

        if ($result) {
            $this->sendPaymentReceipt($payment);
            $this->checkBookingPaymentStatus($payment->booking);
        }

        return $result;
    }

    /**
     * Send payment receipt
     */
    public function sendPaymentReceipt(Payment $payment): void
    {
        if ($payment->booking->inquiry->client) {
            Mail::to($payment->booking->inquiry->client->email)
                ->send(new PaymentReceiptMail($payment));
        }
    }

    /**
     * Check if booking is fully paid
     */
    public function checkBookingPaymentStatus(BookingFile $bookingFile): void
    {
        if ($bookingFile->isFullyPaid()) {
            $bookingFile->update(['status' => \App\Enums\BookingStatus::CONFIRMED]);
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $payments = Payment::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_payments' => $payments->count(),
            'paid_payments' => $payments->where('status', PaymentStatus::PAID)->count(),
            'pending_payments' => $payments->where('status', PaymentStatus::PENDING)->count(),
            'not_paid_payments' => $payments->where('status', PaymentStatus::NOT_PAID)->count(),
            'total_amount' => $payments->sum('amount'),
            'paid_amount' => $payments->where('status', PaymentStatus::PAID)->sum('amount'),
            'pending_amount' => $payments->where('status', PaymentStatus::PENDING)->sum('amount'),
            'not_paid_amount' => $payments->where('status', PaymentStatus::NOT_PAID)->sum('amount'),
        ];
    }

    /**
     * Get overdue payments
     */
    public function getOverduePayments(int $daysOverdue = 30): \Illuminate\Database\Eloquent\Collection
    {
        $cutoffDate = now()->subDays($daysOverdue);

        return Payment::where('status', PaymentStatus::NOT_PAID)
            ->where('created_at', '<', $cutoffDate)
            ->with(['booking.inquiry.client'])
            ->get();
    }

    /**
     * Send overdue payment notifications
     */
    public function sendOverduePaymentNotifications(): int
    {
        $overduePayments = $this->getOverduePayments();
        $sentCount = 0;

        foreach ($overduePayments as $payment) {
            $client = $payment->booking->inquiry->client;
            $daysOverdue = $payment->created_at->diffInDays(now());

            if ($client) {
                $client->notify(new PaymentOverdueNotification($payment, $daysOverdue));
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Generate monthly statement for client
     */
    public function generateMonthlyStatement(Client $client, Carbon $startDate, Carbon $endDate): array
    {
        $bookings = BookingFile::whereHas('inquiry', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $payments = Payment::whereHas('booking.inquiry', function ($query) use ($client) {
                $query->where('client_id', $client->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'client' => $client,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'total_bookings' => $bookings->count(),
            'total_payments' => $payments->count(),
            'total_amount' => $bookings->sum('total_amount'),
            'paid_amount' => $payments->where('status', PaymentStatus::PAID)->sum('amount'),
            'bookings' => $bookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'file_name' => $booking->file_name,
                    'total_amount' => $booking->total_amount,
                    'currency' => $booking->currency,
                    'status' => $booking->status->getLabel(),
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'payments' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'reference_number' => $payment->reference_number,
                    'amount' => $payment->amount,
                    'currency' => $payment->booking->currency,
                    'status' => $payment->status->getLabel(),
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];
    }

    /**
     * Send monthly statements to all clients
     */
    public function sendMonthlyStatements(Carbon $startDate = null, Carbon $endDate = null): int
    {
        $startDate = $startDate ?? now()->subMonth()->startOfMonth();
        $endDate = $endDate ?? now()->subMonth()->endOfMonth();

        $clients = Client::whereHas('inquiries.bookingFile', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })->get();

        $sentCount = 0;

        foreach ($clients as $client) {
            $statementData = $this->generateMonthlyStatement($client, $startDate, $endDate);
            
            Mail::to($client->email)
                ->send(new MonthlyStatementMail($client, $startDate, $endDate, $statementData));
            
            $sentCount++;
        }

        return $sentCount;
    }

    /**
     * Get revenue by period
     */
    public function getRevenueByPeriod(Carbon $startDate, Carbon $endDate, string $groupBy = 'day'): array
    {
        $payments = Payment::where('status', PaymentStatus::PAID)
            ->whereBetween('paid_at', [$startDate, $endDate]);

        switch ($groupBy) {
            case 'hour':
                return $payments->selectRaw('HOUR(paid_at) as period, SUM(amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'day':
                return $payments->selectRaw('DATE(paid_at) as period, SUM(amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'week':
                return $payments->selectRaw('WEEK(paid_at) as period, SUM(amount) as revenue')
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get()
                    ->pluck('revenue', 'period')
                    ->toArray();

            case 'month':
                return $payments->selectRaw('MONTH(paid_at) as period, SUM(amount) as revenue')
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
     * Get payment methods statistics
     */
    public function getPaymentMethodsStatistics(Carbon $startDate = null, Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', PaymentStatus::PAID);

        return $payments->selectRaw('gateway, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('gateway')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->gateway => [
                        'count' => $item->count,
                        'total_amount' => $item->total_amount,
                        'percentage' => 0, // Will be calculated separately
                    ]
                ];
            })
            ->toArray();
    }

    /**
     * Get aging buckets for payments
     */
    public function getAgingBuckets(): array
    {
        $buckets = [
            'current' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(30))
                ->get(),
            '31_60_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(60))
                ->where('created_at', '<', now()->subDays(30))
                ->get(),
            '61_90_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(90))
                ->where('created_at', '<', now()->subDays(60))
                ->get(),
            'over_90_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '<', now()->subDays(90))
                ->get(),
        ];

        $summary = [];
        foreach ($buckets as $key => $payments) {
            $summary[$key] = [
                'count' => $payments->count(),
                'amount' => $payments->sum('amount'),
            ];
        }

        return [
            'buckets' => $buckets,
            'summary' => $summary,
        ];
    }
}
