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

                    <!-- Charts Row -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="booking-status-chart"
                                type="doughnut"
                                :labels="array_column($statusData, 'label')"
                                :data="array_column($statusData, 'count')"
                                title="Booking Status Distribution"
                                subtitle="Breakdown of bookings by status"
                                height="350px"
                                :colors="['#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#ec4899', '#84cc16', '#f97316']"
                                :statistics="[
                                    [
                                        'label' => 'Total Bookings',
                                        'value' => $bookings->count(),
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Total Revenue',
                                        'value' => '$' . number_format($revenueData['total_revenue'], 2),
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-advanced-chart 
                                id="booking-trend-chart"
                                type="line"
                                :labels="array_column($monthlyData, 'period')"
                                :datasets="[
                                    [
                                        'label' => 'Total Bookings',
                                        'data' => array_column($monthlyData, 'total'),
                                        'backgroundColor' => 'rgba(6, 182, 212, 0.1)',
                                        'borderColor' => '#06b6d4',
                                        'borderWidth' => 3,
                                        'fill' => true,
                                        'tension' => 0.4
                                    ],
                                    [
                                        'label' => 'Confirmed',
                                        'data' => array_column($monthlyData, 'confirmed'),
                                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                                        'borderColor' => '#10b981',
                                        'borderWidth' => 3,
                                        'fill' => true,
                                        'tension' => 0.4
                                    ],
                                    [
                                        'label' => 'Completed',
                                        'data' => array_column($monthlyData, 'completed'),
                                        'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                                        'borderColor' => '#8b5cf6',
                                        'borderWidth' => 3,
                                        'fill' => true,
                                        'tension' => 0.4
                                    ],
                                    [
                                        'label' => 'Pending',
                                        'data' => array_column($monthlyData, 'pending'),
                                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                                        'borderColor' => '#f59e0b',
                                        'borderWidth' => 3,
                                        'fill' => true,
                                        'tension' => 0.4
                                    ]
                                ]"
                                title="Booking Trend Analysis"
                                subtitle="Detailed booking volume and status breakdown over time"
                                height="350px"
                                :gradient="true"
                                :animation="true"
                                :statistics="[
                                    [
                                        'label' => 'Avg Daily',
                                        'value' => isset($bookingAnalytics['avg_daily_bookings']) ? $bookingAnalytics['avg_daily_bookings'] : '0',
                                        'color' => 'info'
                                    ],
                                    [
                                        'label' => 'Peak Day',
                                        'value' => isset($bookingAnalytics['peak_day']) ? $bookingAnalytics['peak_day']['formatted_date'] . ' (' . $bookingAnalytics['peak_day']['count'] . ')' : 'N/A',
                                        'color' => 'warning'
                                    ],
                                    [
                                        'label' => 'Growth Rate',
                                        'value' => isset($bookingAnalytics['growth_rate']) ? $bookingAnalytics['growth_rate'] . '%' : '0%',
                                        'color' => isset($bookingAnalytics['growth_rate']) && $bookingAnalytics['growth_rate'] >= 0 ? 'success' : 'danger'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Enhanced Top Clients Chart -->
                    @if($clientBookingData->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <x-advanced-chart 
                                id="top-clients-booking-chart"
                                type="bar"
                                :labels="array_column($clientBookingData->take(10)->toArray(), 'client_name')"
                                :datasets="[
                                    [
                                        'label' => 'Total Revenue',
                                        'data' => array_column($clientBookingData->take(10)->toArray(), 'total_revenue'),
                                        'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                        'borderColor' => '#8b5cf6',
                                        'borderWidth' => 1
                                    ],
                                    [
                                        'label' => 'Paid Revenue',
                                        'data' => array_column($clientBookingData->take(10)->toArray(), 'paid_revenue'),
                                        'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                        'borderColor' => '#10b981',
                                        'borderWidth' => 1
                                    ],
                                    [
                                        'label' => 'Outstanding',
                                        'data' => array_column($clientBookingData->take(10)->toArray(), 'outstanding_revenue'),
                                        'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                                        'borderColor' => '#f59e0b',
                                        'borderWidth' => 1
                                    ]
                                ]"
                                title="Top Clients by Booking Revenue"
                                subtitle="Highest revenue generating clients with payment breakdown"
                                height="350px"
                                :gradient="true"
                                :animation="true"
                                :statistics="[
                                    [
                                        'label' => 'Top Client',
                                        'value' => $clientBookingData->count() > 0 ? $clientBookingData->first()['client_name'] : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Max Revenue',
                                        'value' => '$' . number_format($clientBookingData->count() > 0 ? $clientBookingData->first()['total_revenue'] : 0, 2),
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Best Payment Rate',
                                        'value' => $clientBookingData->count() > 0 ? $clientBookingData->sortByDesc('payment_completion_rate')->first()['payment_completion_rate'] . '%' : 'N/A',
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Booking Insights
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-primary mb-1">{{ $clientBookingData->count() }}</h4>
                                                <small class="text-muted">Active Clients</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-success mb-1">{{ $bookingAnalytics['payment_completion_rate'] }}%</h4>
                                                <small class="text-muted">Payment Rate</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-info mb-1">${{ number_format($bookingAnalytics['avg_booking_value'], 0) }}</h4>
                                                <small class="text-muted">Avg Value</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-warning mb-1">${{ number_format($bookingAnalytics['outstanding_revenue'], 0) }}</h4>
                                                <small class="text-muted">Outstanding</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6 class="mb-3">Top Performers</h6>
                                    @foreach($clientBookingData->take(3) as $index => $client)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }}">
                                                #{{ $index + 1 }}
                                            </span>
                                            <strong>{{ Str::limit($client['client_name'], 15) }}</strong>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">${{ number_format($client['total_revenue'], 0) }}</small><br>
                                            <small class="text-success">{{ $client['payment_completion_rate'] }}% paid</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Analytics Charts -->
                    <div class="row mt-3" style="padding-top: 70px;">
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="hourly-booking-distribution-chart"
                                type="bar"
                                :labels="array_column($bookingAnalytics['hourly_distribution'], 'label')"
                                :data="array_column($bookingAnalytics['hourly_distribution'], 'count')"
                                title="Hourly Booking Distribution"
                                subtitle="Peak hours for booking submissions"
                                    height="350px"
                                :colors="['#8b5cf6']"
                                :statistics="[
                                    [
                                        'label' => 'Peak Hour',
                                        'value' => isset($bookingAnalytics['hourly_distribution']) && count($bookingAnalytics['hourly_distribution']) > 0 ? collect($bookingAnalytics['hourly_distribution'])->sortByDesc('count')->first()['label'] ?? 'N/A' : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Peak Count',
                                        'value' => isset($bookingAnalytics['hourly_distribution']) && count($bookingAnalytics['hourly_distribution']) > 0 ? collect($bookingAnalytics['hourly_distribution'])->max('count') : 0,
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="payment-rate-trend-chart"
                                type="line"
                                :labels="array_column($monthlyData, 'period')"
                                :data="array_column($monthlyData, 'payment_rate')"
                                title="Payment Rate Trend"
                                subtitle="Payment completion rate changes over time"
                                height="350px"
                                :colors="['#10b981']"
                                :statistics="[
                                    [
                                        'label' => 'Avg Payment Rate',
                                        'value' => count($monthlyData) > 0 ? round(array_sum(array_column($monthlyData, 'payment_rate')) / count($monthlyData), 1) . '%' : '0%',
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Best Period',
                                        'value' => count($monthlyData) > 0 ? max(array_column($monthlyData, 'payment_rate')) . '%' : '0%',
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
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
