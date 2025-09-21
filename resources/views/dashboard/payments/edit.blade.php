@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Payment">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.payments.index') }}">Payments</a></li>
            <li class="breadcrumb-item active">Edit #{{ $payment->id }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Edit Payment #{{ $payment->id }}</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.payments.update', $payment) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="booking_id" class="form-label">Booking File <span class="text-danger">*</span></label>
                                            <select name="booking_id" id="booking_id" class="form-select @error('booking_id') is-invalid @enderror" required>
                                                <option value="">Select Booking File</option>
                                                @foreach($bookings as $booking)
                                                    <option value="{{ $booking->id }}" 
                                                            data-client="{{ $booking->inquiry->client->name ?? 'N/A' }}"
                                                            data-amount="{{ $booking->total_amount }}"
                                                            data-currency="{{ $booking->currency }}"
                                                            {{ old('booking_id', $payment->booking_id) == $booking->id ? 'selected' : '' }}>
                                                        {{ $booking->file_name }} - {{ $booking->inquiry->client->name ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('booking_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="gateway" class="form-label">Payment Gateway <span class="text-danger">*</span></label>
                                            <select name="gateway" id="gateway" class="form-select @error('gateway') is-invalid @enderror" required>
                                                <option value="">Select Gateway</option>
                                                @foreach($gateways as $gateway)
                                                    <option value="{{ $gateway }}" {{ old('gateway', $payment->gateway) == $gateway ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_', ' ', $gateway)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('gateway')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text" id="currency-symbol">{{ $payment->booking->currency ?? 'USD' }}</span>
                                                <input type="number" name="amount" id="amount" 
                                                       class="form-control @error('amount') is-invalid @enderror" 
                                                       step="0.01" min="0" value="{{ old('amount', $payment->amount) }}" required>
                                                @error('amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status', $payment->status->value) == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label">Reference Number</label>
                                            <input type="text" name="reference_number" id="reference_number" 
                                                   class="form-control @error('reference_number') is-invalid @enderror" 
                                                   value="{{ old('reference_number', $payment->reference_number) }}">
                                            @error('reference_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="paid_at" class="form-label">Paid At</label>
                                            <input type="datetime-local" name="paid_at" id="paid_at" 
                                                   class="form-control @error('paid_at') is-invalid @enderror" 
                                                   value="{{ old('paid_at', $payment->paid_at ? $payment->paid_at->format('Y-m-d\TH:i') : '') }}">
                                            @error('paid_at')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" 
                                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $payment->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Payment</button>
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
    const bookingSelect = document.getElementById('booking_id');
    const amountInput = document.getElementById('amount');
    const currencySymbol = document.getElementById('currency-symbol');
    const statusSelect = document.getElementById('status');
    const paidAtInput = document.getElementById('paid_at');

    // Update currency and amount when booking is selected
    bookingSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const currency = selectedOption.getAttribute('data-currency');
            const amount = selectedOption.getAttribute('data-amount');
            
            currencySymbol.textContent = currency || 'USD';
            amountInput.value = amount || '';
        } else {
            currencySymbol.textContent = 'USD';
            amountInput.value = '';
        }
    });

    // Auto-fill paid_at when status is set to paid
    statusSelect.addEventListener('change', function() {
        if (this.value === 'paid' && !paidAtInput.value) {
            const now = new Date();
            const localDateTime = now.toISOString().slice(0, 16);
            paidAtInput.value = localDateTime;
        } else if (this.value !== 'paid') {
            paidAtInput.value = '';
        }
    });
});
</script>
@endpush

