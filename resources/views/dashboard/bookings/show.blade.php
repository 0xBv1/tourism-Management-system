@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Booking Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.bookings.index') }}">Bookings</a></li>
            <li class="breadcrumb-item active">#{{ $booking->id }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Main Booking Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Booking #{{ $booking->id }} - {{ $booking->file_name }}</h5>
                            <div class="card-header-right">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.bookings.download', $booking) }}" class="btn btn-success btn-sm">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                    <a href="{{ route('dashboard.bookings.send', $booking) }}" class="btn btn-info btn-sm ms-1">
                                        <i class="fa fa-paper-plane"></i> Send
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">File Name:</label>
                                                <p class="form-control-plaintext">{{ $booking->file_name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status:</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-{{ $booking->status_color }}">
                                                        {{ $booking->status_label }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">File Status:</label>
                                                <p class="form-control-plaintext">
                                                    @if($booking->fileExists())
                                                        <span class="badge bg-success">
                                                            <i class="fa fa-check"></i> File Available
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="fa fa-times"></i> File Not Found
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">File Path:</label>
                                                <p class="form-control-plaintext text-muted small">
                                                    {{ $booking->file_path }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Total Amount:</label>
                                                <p class="form-control-plaintext">
                                                    @if($booking->total_amount)
                                                        {{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}
                                                    @elseif($booking->inquiry && $booking->inquiry->total_amount)
                                                        {{ $booking->currency }} {{ number_format($booking->inquiry->total_amount, 2) }}
                                                        <small class="text-muted">(from inquiry)</small>
                                                    @else
                                                        Not set
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Amount Paid:</label>
                                                <p class="form-control-plaintext">
                                                    @if($booking->inquiry && $booking->inquiry->paid_amount)
                                                        {{ $booking->currency }} {{ number_format($booking->inquiry->paid_amount, 2) }}
                                                    @else
                                                        {{ $booking->currency }} {{ number_format($booking->total_paid, 2) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Remaining Amount:</label>
                                                <p class="form-control-plaintext">
                                                    @php
                                                        $remainingAmount = $booking->inquiry && $booking->inquiry->remaining_amount ? 
                                                            $booking->inquiry->remaining_amount : $booking->remaining_amount;
                                                    @endphp
                                                    <span class="text-{{ $remainingAmount > 0 ? 'danger' : 'success' }}">
                                                        {{ $booking->currency }} {{ number_format($remainingAmount, 2) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Payment Status:</label>
                                                <p class="form-control-plaintext">
                                                    @php
                                                        $isFullyPaid = $booking->inquiry && $booking->inquiry->remaining_amount ? 
                                                            $booking->inquiry->remaining_amount <= 0 : $booking->isFullyPaid();
                                                    @endphp
                                                    <span class="badge bg-{{ $isFullyPaid ? 'success' : 'warning' }}">
                                                        {{ $isFullyPaid ? 'Fully Paid' : 'Pending Payment' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($booking->notes)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Notes:</label>
                                            <div class="border p-3 rounded bg-light">
                                                {{ $booking->notes }}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Inquiry Information Section -->
                                    @if($booking->inquiry)
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-primary">Inquiry Details</h6>
                                            
                                            <!-- Guest Information -->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Guest Name:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->guest_name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Email:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->email ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Phone:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->phone ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Subject:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->subject ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Tour Name:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->tour_name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Nationality:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->nationality ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Arrival Date:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ $booking->inquiry->arrival_date ? $booking->inquiry->arrival_date->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Departure Date:</label>
                                                        <p class="form-control-plaintext">
                                                            {{ $booking->inquiry->departure_date ? $booking->inquiry->departure_date->format('M d, Y') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Number of Pax:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->number_pax ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Payment Method:</label>
                                                        <p class="form-control-plaintext">{{ $booking->inquiry->payment_method ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($booking->inquiry->message)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Message:</label>
                                                    <div class="border p-3 rounded bg-light">
                                                        {{ $booking->inquiry->message }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Checklist Section -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold">Checklist Progress</h6>
                                        <div class="progress mb-3" style="height: 25px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $booking->checklist_progress }}%" 
                                                 aria-valuenow="{{ $booking->checklist_progress }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $booking->checklist_progress }}%
                                            </div>
                                        </div>
                                        
                                        @php
                                            $defaultChecklist = [
                                                'accommodation_booked' => 'Accommodation Booked',
                                                'tours_scheduled' => 'Tours Scheduled',
                                                'transportation_arranged' => 'Transportation Arranged',
                                                'insurance_processed' => 'Insurance Processed',
                                                'final_documents_sent' => 'Final Documents Sent',
                                                'payment_confirmed' => 'Payment Confirmed',
                                                'client_notified' => 'Client Notified'
                                            ];
                                            
                                            $checklist = $booking->checklist ?? [];
                                            $allChecklistItems = array_merge($defaultChecklist, $checklist);
                                        @endphp
                                        
                                        <div class="row">
                                            @foreach($allChecklistItems as $item => $label)
                                                @php
                                                    $isCompleted = isset($checklist[$item]) ? $checklist[$item] : false;
                                                @endphp
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input checklist-item" 
                                                               type="checkbox" 
                                                               data-item="{{ $item }}"
                                                               data-booking="{{ $booking->id }}"
                                                               {{ $isCompleted ? 'checked' : '' }}>
                                                        <label class="form-check-label {{ $isCompleted ? 'text-decoration-line-through' : '' }}">
                                                            {{ is_string($label) ? $label : ucfirst(str_replace('_', ' ', $item)) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <!-- Status Update Form -->
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Update Booking</h6>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('dashboard.bookings.update', $booking) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select">
                                                        @foreach($statuses as $value => $label)
                                                            <option value="{{ $value }}" {{ $booking->status->value === $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Total Amount</label>
                                                    <input type="number" name="total_amount" class="form-control" 
                                                           value="{{ $booking->total_amount }}" step="0.01" min="0">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Currency</label>
                                                    <select name="currency" class="form-select">
                                                        <option value="USD" {{ $booking->currency === 'USD' ? 'selected' : '' }}>USD</option>
                                                        <option value="EUR" {{ $booking->currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                                        <option value="GBP" {{ $booking->currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3">{{ $booking->notes }}</textarea>
                                                </div>

                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fa fa-save"></i> Update Booking
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Timeline -->
                                    <div class="card mt-3">
                                        <div class="card-header">
                                            <h6>Timeline</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Generated At:</label>
                                                <p class="form-control-plaintext">
                                                    {{ $booking->generated_at ? $booking->generated_at->format('M d, Y H:i') : 'Not generated' }}
                                                </p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Sent At:</label>
                                                <p class="form-control-plaintext">
                                                    {{ $booking->sent_at ? $booking->sent_at->format('M d, Y H:i') : 'Not sent' }}
                                                </p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Downloaded At:</label>
                                                <p class="form-control-plaintext">
                                                    {{ $booking->downloaded_at ? $booking->downloaded_at->format('M d, Y H:i') : 'Not downloaded' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Section -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Payments</h5>
                        </div>
                        <div class="card-body">
                            @if($booking->payments->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Gateway</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($booking->payments as $payment)
                                                <tr>
                                                    <td>{{ $payment->invoice_id ?? '-' }}</td>
                                                    <td>{{ $payment->gateway }}</td>
                                                    <td>{{ $booking->currency }} {{ number_format($payment->amount ?? 0, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-success">Completed</span>
                                                    </td>
                                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($booking->inquiry && ($booking->inquiry->paid_amount > 0 || $booking->inquiry->remaining_amount > 0))
                                <!-- Show inquiry payment data when no direct payments exist -->
                                <div class="alert alert-info">
                                    <h6><i class="fa fa-info-circle"></i> Payment Information from Inquiry</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Total Amount:</strong><br>
                                            {{ $booking->currency }} {{ number_format($booking->inquiry->total_amount, 2) }}
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Amount Paid:</strong><br>
                                            <span class="text-success">{{ $booking->currency }} {{ number_format($booking->inquiry->paid_amount, 2) }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Remaining:</strong><br>
                                            <span class="text-{{ $booking->inquiry->remaining_amount > 0 ? 'danger' : 'success' }}">
                                                {{ $booking->currency }} {{ number_format($booking->inquiry->remaining_amount, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    @if($booking->inquiry->payment_method)
                                        <div class="mt-2">
                                            <strong>Payment Method:</strong> {{ $booking->inquiry->payment_method }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    No payments recorded for this booking.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Inquiry Information -->
                    @if($booking->inquiry)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Related Inquiry</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Client:</label>
                                            <p class="form-control-plaintext">
                                                @if($booking->inquiry->client)
                                                    {{ $booking->inquiry->client->name }}
                                                @elseif($booking->inquiry->guest_name)
                                                    {{ $booking->inquiry->guest_name }}
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Subject:</label>
                                            <p class="form-control-plaintext">{{ $booking->inquiry->subject ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Email:</label>
                                            <p class="form-control-plaintext">{{ $booking->inquiry->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Phone:</label>
                                            <p class="form-control-plaintext">{{ $booking->inquiry->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                @if($booking->inquiry->message)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Message:</label>
                                        <div class="border p-3 rounded">
                                            {{ $booking->inquiry->message }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Related Inquiry</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    No related inquiry found for this booking file.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checklist item updates
    document.querySelectorAll('.checklist-item').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const item = this.dataset.item;
            const bookingId = this.dataset.booking;
            const completed = this.checked;
            
            fetch(`/dashboard/bookings/${bookingId}/checklist`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item: item,
                    completed: completed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Update progress bar
                    const progressBar = document.querySelector('.progress-bar');
                    progressBar.style.width = data.progress + '%';
                    progressBar.setAttribute('aria-valuenow', data.progress);
                    progressBar.textContent = data.progress + '%';
                    
                    // Update label style
                    const label = this.nextElementSibling;
                    if (completed) {
                        label.classList.add('text-decoration-line-through');
                    } else {
                        label.classList.remove('text-decoration-line-through');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert checkbox state
                this.checked = !completed;
            });
        });
    });
});
</script>
@endpush
