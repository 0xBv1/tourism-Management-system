@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Finance Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Finance</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Role Indicator -->
                    @if(admin()->roles->count() > 0)
                        <div class="alert alert-info">
                            <i class="fa fa-user"></i> 
                            <strong>Current Role:</strong> {{ admin()->roles->pluck('name')->join(', ') }}
                        </div>
                    @endif
                    
                    <!-- Filter Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Filter Finance Report</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.finance') }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" 
                                                   class="form-control" 
                                                   value="{{ $startDate->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date</label>
                                            <input type="date" name="end_date" id="end_date" 
                                                   class="form-control" 
                                                   value="{{ $endDate->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary">Filter</button>
                                                <a href="{{ route('dashboard.reports.finance') }}" class="btn btn-secondary">Reset</a>
                                                <a href="{{ route('dashboard.reports.export', 'finance') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
                                                    <i class="fa fa-download"></i> Export
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-gradient-primary text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">{{ $payments->count() }}</h3>
                                            <p class="mb-1 fs-6">Total Payments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-credit-card fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-success text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">${{ number_format($payments->where('status', \App\Enums\PaymentStatus::PAID)->sum('amount'), 2) }}</h3>
                                            <p class="mb-1 fs-6">Paid Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-check-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-warning text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">${{ number_format($payments->where('status', \App\Enums\PaymentStatus::PENDING)->sum('amount'), 2) }}</h3>
                                            <p class="mb-1 fs-6">Pending Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-clock fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-danger text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">${{ number_format($payments->where('status', \App\Enums\PaymentStatus::NOT_PAID)->sum('amount'), 2) }}</h3>
                                            <p class="mb-1 fs-6">Not Paid Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-times-circle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="payment-status-chart"
                                type="doughnut"
                                :labels="array_column($statusData, 'label')"
                                :data="array_column($statusData, 'count')"
                                title="Payment Status Distribution"
                                subtitle="Breakdown of payments by status"
                                height="350px"
                                :colors="['#10b981', '#f59e0b', '#ef4444']"
                                :statistics="[
                                    [
                                        'label' => 'Total Payments',
                                        'value' => $payments->count(),
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Paid Amount',
                                        'value' => '$' . number_format($payments->where('status', \App\Enums\PaymentStatus::PAID)->sum('amount'), 2),
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="gateway-distribution-chart"
                                type="pie"
                                :labels="array_column($gatewayData, 'gateway')"
                                :data="array_column($gatewayData, 'count')"
                                title="Gateway Distribution"
                                subtitle="Payment methods used by customers"
                                height="350px"
                                :colors="['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444']"
                                :statistics="[
                                    [
                                        'label' => 'Total Gateways',
                                        'value' => count($gatewayData),
                                        'color' => 'info'
                                    ],
                                    [
                                        'label' => 'Most Used',
                                        'value' => count($gatewayData) > 0 ? $gatewayData[0]['gateway'] : 'N/A',
                                        'color' => 'primary'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Payments Details ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="payments-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invoice ID</th>
                                            <th>Reference</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Gateway</th>
                                            <th>Paid At</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->invoice_id ?? 'N/A' }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking?->inquiry?->client?->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status->getColor() }}">
                                                    {{ $payment->status->getLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->gateway)) }}</td>
                                            <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No payments found for the selected period.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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
{{-- Chart.js is now included by the chart components --}}
@endpush

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    
    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .btn {
        border-radius: 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        font-weight: 600;
        color: #495057;
    }
    
    .badge {
        border-radius: 20px;
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endpush
