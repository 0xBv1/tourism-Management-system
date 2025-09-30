@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Payment Statements">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.payments.index') }}">Payments</a></li>
            <li class="breadcrumb-item active">Statements</li>
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
                            <h5>Filter Statements</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.payments.statements') }}">
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
                                                <a href="{{ route('dashboard.payments.statements') }}" class="btn btn-secondary">Reset</a>
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
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['total_payments'] }}</h4>
                                            <p class="mb-0">Total Payments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-credit-card fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($summary['paid_amount'], 2) }}</h4>
                                            <p class="mb-0">Paid Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-check-circle fa-2x"></i>
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
                                            <h4>${{ number_format($summary['pending_amount'], 2) }}</h4>
                                            <p class="mb-0">Pending Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-clock fa-2x"></i>
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
                                            <h4>${{ number_format($summary['not_paid_amount'], 2) }}</h4>
                                            <p class="mb-0">Not Paid Amount</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Payment Statements ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                            <div class="card-header-right">
                                <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                                    <i class="fa fa-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="statements-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invoice ID</th>
                                            <th>Reference</th>
                                            <th>Booking File</th>
                                            <th>Client</th>
                                            <th>Gateway</th>
                                            <th>Amount</th>
                                            <th>Status</th>
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
                                            <td>{{ $payment->booking->file_name ?? 'N/A' }}</td>
                                            <td>{{ $payment->booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->gateway)) }}</td>
                                            <td>{{ $payment->formatted_amount }}</td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status_color }}">
                                                    {{ $payment->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : 'N/A' }}</td>
                                            <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No payments found for the selected period.</td>
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
<script>
function exportToExcel() {
    // Create a simple CSV export
    const table = document.getElementById('statements-table');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = '';
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const rowData = cells.map(cell => {
            // Remove HTML tags and clean up text
            let text = cell.textContent.trim();
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',') || text.includes('"')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            return text;
        });
        csv += rowData.join(',') + '\n';
    });
    
    // Create and download file
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'payment_statements_{{ $startDate->format("Y-m-d") }}_to_{{ $endDate->format("Y-m-d") }}.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endpush

