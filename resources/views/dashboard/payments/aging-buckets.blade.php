@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Aging Buckets Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.payments.index') }}">Payments</a></li>
            <li class="breadcrumb-item active">Aging Buckets</li>
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
                    
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['current']['count'] }}</h4>
                                            <p class="mb-0">Current (0-30 days)</p>
                                            <small>${{ number_format($summary['current']['amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-calendar fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['31_60_days']['count'] }}</h4>
                                            <p class="mb-0">31-60 Days</p>
                                            <small>${{ number_format($summary['31_60_days']['amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['61_90_days']['count'] }}</h4>
                                            <p class="mb-0">61-90 Days</p>
                                            <small>${{ number_format($summary['61_90_days']['amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-exclamation-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-dark text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['over_90_days']['count'] }}</h4>
                                            <p class="mb-0">Over 90 Days</p>
                                            <small>${{ number_format($summary['over_90_days']['amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-ban fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current (0-30 days) -->
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Current (0-30 days) - {{ $buckets['current']->count() }} payments</h5>
                        </div>
                        <div class="card-body">
                            @if($buckets['current']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Reference</th>
                                            <th>Booking File</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Days Overdue</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($buckets['current'] as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>{{ $payment->created_at->diffInDays(now()) }} days</td>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center">No payments in this category.</p>
                            @endif
                        </div>
                    </div>

                    <!-- 31-60 Days -->
                    <div class="card mt-3">
                        <div class="card-header bg-warning text-white">
                            <h5 class="mb-0">31-60 Days - {{ $buckets['31_60_days']->count() }} payments</h5>
                        </div>
                        <div class="card-body">
                            @if($buckets['31_60_days']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Reference</th>
                                            <th>Booking File</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Days Overdue</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($buckets['31_60_days'] as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>{{ $payment->created_at->diffInDays(now()) }} days</td>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center">No payments in this category.</p>
                            @endif
                        </div>
                    </div>

                    <!-- 61-90 Days -->
                    <div class="card mt-3">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">61-90 Days - {{ $buckets['61_90_days']->count() }} payments</h5>
                        </div>
                        <div class="card-body">
                            @if($buckets['61_90_days']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Reference</th>
                                            <th>Booking File</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Days Overdue</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($buckets['61_90_days'] as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>{{ $payment->created_at->diffInDays(now()) }} days</td>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center">No payments in this category.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Over 90 Days -->
                    <div class="card mt-3">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Over 90 Days - {{ $buckets['over_90_days']->count() }} payments</h5>
                        </div>
                        <div class="card-body">
                            @if($buckets['over_90_days']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Reference</th>
                                            <th>Booking File</th>
                                            <th>Client</th>
                                            <th>Amount</th>
                                            <th>Days Overdue</th>
                                            <th>Created At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($buckets['over_90_days'] as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->reference_number ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>{{ $payment->created_at->diffInDays(now()) }} days</td>
                                            <td>{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('dashboard.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-muted text-center">No payments in this category.</p>
                            @endif
                        </div>
                    </div>

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
                                    <button class="btn btn-success" onclick="exportAgingReport()">
                                        <i class="fa fa-file-excel"></i> Export Report
                                    </button>
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
function exportAgingReport() {
    // Create a simple CSV export for aging report
    let csv = 'Aging Buckets Report\n\n';
    
    // Add summary
    csv += 'Category,Count,Amount\n';
    csv += `Current (0-30 days),${$summary['current']['count']},${$summary['current']['amount']}\n`;
    csv += `31-60 Days,${$summary['31_60_days']['count']},${$summary['31_60_days']['amount']}\n`;
    csv += `61-90 Days,${$summary['61_90_days']['count']},${$summary['61_90_days']['amount']}\n`;
    csv += `Over 90 Days,${$summary['over_90_days']['count']},${$summary['over_90_days']['amount']}\n\n`;
    
    // Add detailed data
    csv += 'ID,Reference,Booking File,Client,Amount,Days Overdue,Created At\n';
    
    // Add all payments from all buckets
    const allPayments = [
        ...@json($buckets['current']),
        ...@json($buckets['31_60_days']),
        ...@json($buckets['61_90_days']),
        ...@json($buckets['over_90_days'])
    ];
    
    allPayments.forEach(payment => {
        const daysOverdue = Math.floor((new Date() - new Date(payment.created_at)) / (1000 * 60 * 60 * 24));
        csv += `${payment.id},"${payment.reference_number || 'N/A'}","${payment.booking?.file_name || 'N/A'}","${payment.booking?.inquiry?.client?.name || 'N/A'}",${payment.amount},${daysOverdue},"${new Date(payment.created_at).toLocaleDateString()}"\n`;
    });
    
    // Create and download file
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'aging_buckets_report_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endpush

