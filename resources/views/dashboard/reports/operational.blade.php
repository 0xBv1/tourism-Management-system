@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Operational Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Operational</li>
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
                            <h5>Filter Operational Report</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.operational') }}">
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
                                                <a href="{{ route('dashboard.reports.operational') }}" class="btn btn-secondary">Reset</a>
                                                <a href="{{ route('dashboard.reports.export', 'operational') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
                                                    <i class="fa fa-download"></i> Export
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Revenue Analytics Summary -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($revenueAnalytics['total_revenue'], 0) }}</h4>
                                            <p class="mb-0">Total Revenue</p>
                                            <small>{{ $revenueAnalytics['payment_completion_rate'] }}% Paid</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($revenueAnalytics['booking_revenue'], 0) }}</h4>
                                            <p class="mb-0">Booking Revenue</p>
                                            <small>{{ $revenueAnalytics['total_bookings'] }} Bookings</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-file-invoice-dollar fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>${{ number_format($revenueAnalytics['inquiry_revenue'], 0) }}</h4>
                                            <p class="mb-0">Inquiry Revenue</p>
                                            <small>{{ $revenueAnalytics['total_inquiries'] }} Inquiries</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-question-circle fa-2x"></i>
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
                                            <h4>{{ $revenueAnalytics['conversion_rate'] }}%</h4>
                                            <p class="mb-0">Conversion Rate</p>
                                            <small>Inquiry to Booking</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-chart-line fa-2x"></i>
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
                                id="resource-utilization-chart"
                                type="bar"
                                :labels="array_column($resourceUtilizationData, 'label')"
                                :data="array_column($resourceUtilizationData, 'avg_utilization')"
                                title="Resource Utilization Overview"
                                subtitle="Average utilization by resource type"
                                height="350px"
                                :colors="['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b']"
                                :statistics="[
                                    [
                                        'label' => 'Best Utilized',
                                        'value' => max(array_column($resourceUtilizationData, 'avg_utilization')) . '%',
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Avg Utilization',
                                        'value' => round(array_sum(array_column($resourceUtilizationData, 'avg_utilization')) / count($resourceUtilizationData), 1) . '%',
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-advanced-chart 
                                id="revenue-breakdown-chart"
                                type="bar"
                                :labels="['Booking Revenue', 'Inquiry Revenue']"
                                :datasets="[
                                    [
                                        'label' => 'Total Revenue',
                                        'data' => [$revenueAnalytics['booking_revenue'], $revenueAnalytics['inquiry_revenue']],
                                        'backgroundColor' => ['rgba(139, 92, 246, 0.8)', 'rgba(6, 182, 212, 0.8)'],
                                        'borderColor' => ['#8b5cf6', '#06b6d4'],
                                        'borderWidth' => 1
                                    ]
                                ]"
                                title="Revenue Breakdown"
                                subtitle="Booking vs Inquiry revenue comparison"
                                height="350px"
                                :gradient="true"
                                :animation="true"
                                :statistics="[
                                    [
                                        'label' => 'Total Revenue',
                                        'value' => '$' . number_format($revenueAnalytics['total_revenue'], 2),
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Paid Revenue',
                                        'value' => '$' . number_format($revenueAnalytics['paid_revenue'], 2),
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Outstanding',
                                        'value' => '$' . number_format($revenueAnalytics['outstanding_revenue'], 2),
                                        'color' => 'warning'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Staff Performance Chart -->
                    @if($staffPerformance->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <x-dashboard-chart 
                                id="staff-performance-chart"
                                type="bar"
                                :labels="array_column($staffPerformance->take(10)->toArray(), 'user.name')"
                                :data="array_column($staffPerformance->take(10)->toArray(), 'inquiries_handled')"
                                title="Staff Performance"
                                subtitle="Inquiries handled by staff members"
                                height="300px"
                                :colors="['#8b5cf6']"
                                :statistics="[
                                    [
                                        'label' => 'Top Performer',
                                        'value' => $staffPerformance->count() > 0 ? $staffPerformance->first()['user']->name : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Max Inquiries',
                                        'value' => $staffPerformance->count() > 0 ? $staffPerformance->first()['inquiries_handled'] : 0,
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>
                    @endif

                    <!-- Resource Utilization Summary -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $hotelUtilization->count() }}</h4>
                                            <p class="mb-0">Hotels</p>
                                            <small>Avg: {{ $hotelUtilization->avg('utilization_percentage') }}%</small>
                                            <br><small>Revenue: ${{ number_format($hotelUtilization->sum('total_revenue'), 0) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-bed fa-2x"></i>
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
                                            <h4>{{ $vehicleUtilization->count() }}</h4>
                                            <p class="mb-0">Vehicles</p>
                                            <small>Avg: {{ $vehicleUtilization->avg('utilization_percentage') }}%</small>
                                            <br><small>Revenue: ${{ number_format($vehicleUtilization->sum('total_revenue'), 0) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-car fa-2x"></i>
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
                                            <h4>{{ $guideUtilization->count() }}</h4>
                                            <p class="mb-0">Guides</p>
                                            <small>Avg: {{ $guideUtilization->avg('utilization_percentage') }}%</small>
                                            <br><small>Revenue: ${{ number_format($guideUtilization->sum('total_revenue'), 0) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-user fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $representativeUtilization->count() }}</h4>
                                            <p class="mb-0">Representatives</p>
                                            <small>Avg: {{ $representativeUtilization->avg('utilization_percentage') }}%</small>
                                            <br><small>Revenue: ${{ number_format($representativeUtilization->sum('total_revenue'), 0) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-handshake fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resource Utilization Tables -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Hotel Utilization</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Hotel</th>
                                                    <th>Utilization</th>
                                                    <th>Bookings</th>
                                                    <th>Total Revenue</th>
                                                    <th>Booking Rev.</th>
                                                    <th>Inquiry Rev.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hotelUtilization->take(5) as $util)
                                                <tr>
                                                    <td>{{ $util['resource']->name }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                 style="width: {{ $util['utilization_percentage'] }}%">
                                                                {{ $util['utilization_percentage'] }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $util['bookings_count'] }}</td>
                                                    <td><strong>${{ number_format($util['total_revenue'], 2) }}</strong></td>
                                                    <td>${{ number_format($util['booking_revenue'], 2) }}</td>
                                                    <td>${{ number_format($util['inquiry_revenue'], 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Vehicle Utilization</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Vehicle</th>
                                                    <th>Utilization</th>
                                                    <th>Bookings</th>
                                                    <th>Total Revenue</th>
                                                    <th>Booking Rev.</th>
                                                    <th>Inquiry Rev.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehicleUtilization->take(5) as $util)
                                                <tr>
                                                    <td>{{ $util['resource']->name }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-success" role="progressbar" 
                                                                 style="width: {{ $util['utilization_percentage'] }}%">
                                                                {{ $util['utilization_percentage'] }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $util['bookings_count'] }}</td>
                                                    <td><strong>${{ number_format($util['total_revenue'], 2) }}</strong></td>
                                                    <td>${{ number_format($util['booking_revenue'], 2) }}</td>
                                                    <td>${{ number_format($util['inquiry_revenue'], 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Performance -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Staff Performance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Staff Member</th>
                                                    <th>Inquiries Handled</th>
                                                    <th>Inquiries Confirmed</th>
                                                    <th>Conversion Rate</th>
                                                    <th>Assignment Types</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($staffPerformance as $perf)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $perf['user']->name }}</strong>
                                                            <br><small class="text-muted">{{ $perf['user']->email }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary">{{ $perf['inquiries_handled'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-success">{{ $perf['inquiries_confirmed'] }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-{{ $perf['conversion_rate'] >= 50 ? 'success' : ($perf['conversion_rate'] >= 25 ? 'warning' : 'danger') }}">
                                                            {{ $perf['conversion_rate'] }}%
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @foreach($perf['assignment_types'] as $type)
                                                            <span class="badge bg-info me-1">{{ $type }}</span>
                                                        @endforeach
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No staff performance data available.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Resource Bookings -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Resource Bookings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Resource</th>
                                                    <th>Type</th>
                                                    <th>Client</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Total Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($resourceBookings->take(10) as $booking)
                                                <tr>
                                                    <td>{{ $booking->resource_name }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $booking->resource_type === 'hotel' ? 'primary' : ($booking->resource_type === 'vehicle' ? 'success' : 'info') }}">
                                                            {{ ucfirst($booking->resource_type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $booking->bookingFile->inquiry->client->name ?? 'N/A' }}</td>
                                                    <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                                    <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                                    <td>${{ number_format($booking->total_price, 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">No resource bookings found for the selected period.</td>
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
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
