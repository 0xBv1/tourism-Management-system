@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Bookings Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Bookings</li>
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
                            <h5>Filter Bookings Report</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.bookings') }}">
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
                                                <a href="{{ route('dashboard.reports.bookings') }}" class="btn btn-secondary">Reset</a>
                                                <a href="{{ route('dashboard.reports.export', 'bookings') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
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
                                            <h3 class="fw-bold">{{ $bookings->count() }}</h3>
                                            <p class="mb-1 fs-6">Total Bookings</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-file-text fa-2x"></i>
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
                                            <h3 class="fw-bold">${{ number_format($revenueData['total_revenue'], 2) }}</h3>
                                            <p class="mb-1 fs-6">Total Income</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-dollar-sign fa-2x"></i>
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
                                            <h3 class="fw-bold">${{ number_format($revenueData['average_booking_value'], 2) }}</h3>
                                            <p class="mb-1 fs-6">Avg Booking Value</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-chart-line fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-gradient-info text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">${{ number_format($revenueData['outstanding_amount'], 2) }}</h3>
                                            <p class="mb-1 fs-6">Outstanding</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-exclamation-triangle fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Bookings Table -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Bookings Details ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="bookings-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>File Name</th>
                                            <th>Client</th>
                                            <th>Status</th>
                                            <th>Total Amount</th>
                                            <th>Paid Amount</th>
                                            <th>Remaining</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->file_name }}</td>
                                            <td>{{ $booking->inquiry->client->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $booking->status->getColor() }}">
                                                    {{ $booking->status->getLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $booking->currency }} {{ number_format($booking->total_amount, 2) }}</td>
                                            <td>{{ $booking->currency }} {{ number_format($booking->total_paid, 2) }}</td>
                                            <td>{{ $booking->currency }} {{ number_format($booking->remaining_amount, 2) }}</td>
                                            <td>{{ $booking->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No bookings found for the selected period.</td>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart initialization code removed
});
</script>
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
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
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
