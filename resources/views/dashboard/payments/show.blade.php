@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Payment Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.payments.index') }}">Payments</a></li>
            <li class="breadcrumb-item active">#{{ $payment->id }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Payment Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Payment #{{ $payment->id }}</h5>
                            <div class="card-header-right">
                                <span class="badge bg-{{ $payment->status_color }} fs-6">{{ $payment->status_label }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Reference Number:</strong></td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Amount:</strong></td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gateway:</strong></td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->gateway)) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status_color }}">{{ $payment->status_label }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Created At:</strong></td>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Paid At:</strong></td>
                                            <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Updated At:</strong></td>
                                            <td>{{ $payment->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($payment->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Notes:</h6>
                                    <p class="text-muted">{{ $payment->notes }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Booking File Details Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Related Booking File</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>File Name:</strong></td>
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Client:</strong></td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td>{{ $payment->booking->currency }} {{ number_format($payment->booking->total_amount, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Booking Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $payment->booking->status_color }}">
                                                    {{ $payment->booking->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Paid:</strong></td>
                                            <td>{{ $payment->booking->currency }} {{ number_format($payment->booking->total_paid, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Remaining:</strong></td>
                                            <td>{{ $payment->booking->currency }} {{ number_format($payment->booking->remaining_amount, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('dashboard.bookings.show', $payment->booking->id) }}" class="btn btn-primary">
                                    View Booking Details
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Details Card -->
                    @if($payment->transaction_request || $payment->transaction_verification)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Transaction Details</h5>
                        </div>
                        <div class="card-body">
                            @if($payment->transaction_request)
                            <div class="mb-3">
                                <h6>Transaction Request:</h6>
                                <pre class="bg-light p-3 rounded"><code>{{ json_encode($payment->transaction_request, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                            @endif
                            
                            @if($payment->transaction_verification)
                            <div class="mb-3">
                                <h6>Transaction Verification:</h6>
                                <pre class="bg-light p-3 rounded"><code>{{ json_encode($payment->transaction_verification, JSON_PRETTY_PRINT) }}</code></pre>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('dashboard.payments.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back to Payments
                                    </a>
                                </div>
                                <div>
                                    @can('payments.edit')
                                    <a href="{{ route('dashboard.payments.edit', $payment) }}" class="btn btn-warning me-2">
                                        <i class="fa fa-edit"></i> Edit Payment
                                    </a>
                                    @endcan
                                    
                                    @if($payment->status->value === 'not_paid')
                                    <button type="button" class="btn btn-success me-2 mark-as-paid-btn" 
                                            data-payment-id="{{ $payment->id }}">
                                        <i class="fa fa-check"></i> Mark as Paid
                                    </button>
                                    @endif
                                    
                                    @can('payments.delete')
                                    <button type="button" class="btn btn-danger delete-btn" 
                                            data-payment-id="{{ $payment->id }}">
                                        <i class="fa fa-trash"></i> Delete Payment
                                    </button>
                                    @endcan
                                </div>
                            </div>
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
    // Mark as paid functionality
    document.querySelectorAll('.mark-as-paid-btn').forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            
            if (confirm('Are you sure you want to mark this payment as paid?')) {
                fetch(`/dashboard/payments/${paymentId}/mark-as-paid`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while marking payment as paid.');
                });
            }
        });
    });

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const paymentId = this.getAttribute('data-payment-id');
            
            if (confirm('Are you sure you want to delete this payment?')) {
                fetch(`/dashboard/payments/${paymentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        alert(data.message);
                        window.location.href = '/dashboard/payments';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the payment.');
                });
            }
        });
    });
});
</script>
@endpush

