<?php

namespace App\Http\Controllers\Dashboard;

use App\DataTables\PaymentDataTable;
use App\Events\PaymentReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PaymentRequest;
use App\Models\Payment;
use App\Models\BookingFile;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentDataTable $dataTable)
    {
        $this->authorize('payments.list');
        return $dataTable->render('dashboard.payments.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('payments.create');
        $bookings = BookingFile::with(['inquiry.client'])
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->where('status', '!=', BookingStatus::REFUNDED)
            ->get();
        
        $statuses = PaymentStatus::options();
        $gateways = ['paypal', 'fawaterk', 'stripe', 'bank_transfer', 'cash'];
        
        return view('dashboard.payments.create', compact('bookings', 'statuses', 'gateways'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentRequest $request)
    {
        $this->authorize('payments.create');
        $payment = Payment::create($request->getSanitized());
        
        // If payment is marked as paid, trigger the event
        if ($payment->status === PaymentStatus::PAID) {
            event(new PaymentReceived($payment));
        }
        
        session()->flash('message', 'Payment Created Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.payments.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $this->authorize('payments.show');
        $payment->load(['booking.inquiry.client']);
        return view('dashboard.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $this->authorize('payments.edit');
        $bookings = BookingFile::with(['inquiry.client'])
            ->where('status', '!=', BookingStatus::CANCELLED)
            ->where('status', '!=', BookingStatus::REFUNDED)
            ->get();
        
        $statuses = PaymentStatus::options();
        $gateways = ['paypal', 'fawaterk', 'stripe', 'bank_transfer', 'cash'];
        
        return view('dashboard.payments.edit', compact('payment', 'bookings', 'statuses', 'gateways'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $this->authorize('payments.edit');
        $oldStatus = $payment->status;
        $payment->update($request->getSanitized());
        
        // If payment status changed to paid, trigger the event
        if ($oldStatus !== PaymentStatus::PAID && $payment->status === PaymentStatus::PAID) {
            event(new PaymentReceived($payment));
        }
        
        session()->flash('message', 'Payment Updated Successfully!');
        session()->flash('type', 'success');
        return redirect()->route('dashboard.payments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $this->authorize('payments.delete');
        $payment->delete();
        return response()->json([
            'message' => 'Payment Deleted Successfully!'
        ]);
    }

    /**
     * Mark payment as paid
     */
    public function markAsPaid(Payment $payment)
    {
        $this->authorize('payments.mark-as-paid');
        $payment->markAsPaid();
        event(new PaymentReceived($payment));
        
        return response()->json([
            'message' => 'Payment marked as paid successfully!'
        ]);
    }

    /**
     * Show payment statements
     */
    public function statements(Request $request)
    {
        $this->authorize('payments.statements');
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        
        if (is_string($startDate)) {
            $startDate = Carbon::parse($startDate);
        }
        if (is_string($endDate)) {
            $endDate = Carbon::parse($endDate);
        }

        $payments = Payment::with(['booking.inquiry.client'])
            ->byDateRange($startDate, $endDate)
            ->orderBy('created_at', 'desc')
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'paid_amount' => $payments->where('status', PaymentStatus::PAID)->sum('amount'),
            'pending_amount' => $payments->where('status', PaymentStatus::PENDING)->sum('amount'),
            'not_paid_amount' => $payments->where('status', PaymentStatus::NOT_PAID)->sum('amount'),
        ];

        return view('dashboard.payments.statements', compact('payments', 'summary', 'startDate', 'endDate'));
    }

    /**
     * Show aging buckets report
     */
    public function agingBuckets(Request $request)
    {
        $this->authorize('payments.aging-buckets');
        $buckets = [
            'current' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(30))
                ->with(['booking.inquiry.client'])
                ->get(),
            '31_60_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(60))
                ->where('created_at', '<', now()->subDays(30))
                ->with(['booking.inquiry.client'])
                ->get(),
            '61_90_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '>=', now()->subDays(90))
                ->where('created_at', '<', now()->subDays(60))
                ->with(['booking.inquiry.client'])
                ->get(),
            'over_90_days' => Payment::where('status', PaymentStatus::NOT_PAID)
                ->where('created_at', '<', now()->subDays(90))
                ->with(['booking.inquiry.client'])
                ->get(),
        ];

        $summary = [];
        foreach ($buckets as $key => $payments) {
            $summary[$key] = [
                'count' => $payments->count(),
                'amount' => $payments->sum('amount'),
            ];
        }

        return view('dashboard.payments.aging-buckets', compact('buckets', 'summary'));
    }
}

