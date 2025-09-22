@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Confirm Inquiry">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.index') }}">Inquiries</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.show', $inquiry) }}">#{{ $inquiry->id }}</a></li>
            <li class="breadcrumb-item active">Confirm</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Confirm Inquiry with Payment Details</h5>
                            <small class="text-muted">Enter payment information to confirm this inquiry</small>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6>Inquiry Details:</h6>
                                    <div class="border p-3 rounded bg-light">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Guest Name:</strong> {{ $inquiry->guest_name }}<br>
                                                <strong>Email:</strong> {{ $inquiry->email }}<br>
                                                <strong>Phone:</strong> {{ $inquiry->phone }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Tour Name:</strong> {{ $inquiry->tour_name ?? 'Not specified' }}<br>
                                                <strong>Arrival Date:</strong> {{ $inquiry->arrival_date?->format('M d, Y') ?? 'Not specified' }}<br>
                                                <strong>Number of Pax:</strong> {{ $inquiry->number_pax ?? 'Not specified' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mb-4">
                                <h6><i class="fa fa-info-circle"></i> Payment Confirmation Process</h6>
                                <p class="mb-0">To confirm this inquiry, please enter the total amount, deposit amount (paid amount), and payment method. The remaining amount will be calculated automatically.</p>
                            </div>

                            <form action="{{ route('dashboard.inquiries.process-confirmation', $inquiry) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="total_amount" class="form-label">Total Amount <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control @error('total_amount') is-invalid @enderror" 
                                                   id="total_amount" name="total_amount" value="{{ old('total_amount', $inquiry->total_amount) }}" 
                                                   min="0" required>
                                            @error('total_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="paid_amount" class="form-label">Paid Amount (Deposit) <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" 
                                                   id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $inquiry->paid_amount) }}" 
                                                   min="0" required>
                                            @error('paid_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                            <select class="form-control @error('payment_method') is-invalid @enderror" 
                                                    id="payment_method" name="payment_method" required>
                                                <option value="">Select Payment Method</option>
                                                <option value="cash" {{ old('payment_method', $inquiry->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="bank_transfer" {{ old('payment_method', $inquiry->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="credit_card" {{ old('payment_method', $inquiry->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                                <option value="paypal" {{ old('payment_method', $inquiry->payment_method) == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                                <option value="other" {{ old('payment_method', $inquiry->payment_method) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('payment_method')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-info">
                                            <h6>Payment Summary:</h6>
                                            <div id="payment-summary">
                                                <p><strong>Total Amount:</strong> $<span id="total-display">0.00</span></p>
                                                <p><strong>Paid Amount:</strong> $<span id="paid-display">0.00</span></p>
                                                <p><strong>Remaining Amount:</strong> $<span id="remaining-display">0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('dashboard.inquiries.show', $inquiry) }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fa fa-check"></i> Confirm Inquiry with Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalInput = document.getElementById('total_amount');
    const paidInput = document.getElementById('paid_amount');
    const totalDisplay = document.getElementById('total-display');
    const paidDisplay = document.getElementById('paid-display');
    const remainingDisplay = document.getElementById('remaining-display');

    function updatePaymentSummary() {
        const total = parseFloat(totalInput.value) || 0;
        const paid = parseFloat(paidInput.value) || 0;
        const remaining = total - paid;

        totalDisplay.textContent = total.toFixed(2);
        paidDisplay.textContent = paid.toFixed(2);
        remainingDisplay.textContent = remaining.toFixed(2);

        // Validate that paid amount doesn't exceed total
        if (paid > total) {
            paidInput.setCustomValidity('Paid amount cannot exceed total amount');
        } else {
            paidInput.setCustomValidity('');
        }
    }

    totalInput.addEventListener('input', updatePaymentSummary);
    paidInput.addEventListener('input', updatePaymentSummary);

    // Initial calculation
    updatePaymentSummary();
});
</script>
@endpush
