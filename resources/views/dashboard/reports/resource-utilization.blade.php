@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Resource Utilization Reports">
            <li class="breadcrumb-item active">Resource Utilization</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Role Indicator -->
                    @if(admin()->roles->count() > 0)
                        <div class="alert alert-info">
                            <i class="fa fa-user-tag"></i> 
                            <strong>Current Role:</strong> {{ admin()->roles->pluck('name')->join(', ') }}
                        </div>
                    @endif
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Resource Utilization Report</h5>
                            <div class="card-header-right">
                                <form method="GET" class="d-inline-flex">
                                    <input type="date" name="start_date" class="form-control me-2" 
                                           value="{{ $startDate->format('Y-m-d') }}" required>
                                    <input type="date" name="end_date" class="form-control me-2" 
                                           value="{{ $endDate->format('Y-m-d') }}" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </form>
                                <a href="{{ route('dashboard.reports.resource-utilization.export', request()->query()) }}" 
                                   class="btn btn-success btn-sm ms-2">
                                    <i class="fa fa-download"></i> Export
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Overall Statistics -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h4>{{ $overallStats['total_resources'] }}</h4>
                                            <p class="mb-0">Total Resources</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h4>{{ $overallStats['total_bookings'] }}</h4>
                                            <p class="mb-0">Total Bookings</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h4>${{ number_format($overallStats['total_revenue'], 2) }}</h4>
                                            <p class="mb-0">Total Revenue</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h4>{{ $overallStats['period_days'] }}</h4>
                                            <p class="mb-0">Period Days</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hotels Utilization -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6>Hotels Utilization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Hotel Name</th>
                                                    <th>City</th>
                                                    <th>Total Rooms</th>
                                                    <th>Utilization %</th>
                                                    <th>Booked Days</th>
                                                    <th>Total Days</th>
                                                    <th>Bookings</th>
                                                    <th>Revenue</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hotelUtilization as $data)
                                                    <tr>
                                                        <td>{{ $data['resource']->name }}</td>
                                                        <td>{{ $data['resource']->city->name ?? '-' }}</td>
                                                        <td>{{ $data['resource']->total_rooms }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $data['utilization_percentage'] > 80 ? 'bg-success' : ($data['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $data['utilization_percentage'] }}%"
                                                                     aria-valuenow="{{ $data['utilization_percentage'] }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $data['utilization_percentage'] }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $data['booked_days'] }}</td>
                                                        <td>{{ $data['total_days'] }}</td>
                                                        <td>{{ $data['bookings_count'] }}</td>
                                                        <td>${{ number_format($data['total_revenue'], 2) }}</td>
                                                        <td>
                                                            <a href="{{ route('dashboard.reports.resource-details', ['hotel', $data['resource']->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                                                               class="btn btn-sm btn-primary">Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Vehicles Utilization -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6>Vehicles Utilization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Vehicle Name</th>
                                                    <th>Type</th>
                                                    <th>City</th>
                                                    <th>Utilization %</th>
                                                    <th>Booked Days</th>
                                                    <th>Total Days</th>
                                                    <th>Bookings</th>
                                                    <th>Revenue</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehicleUtilization as $data)
                                                    <tr>
                                                        <td>{{ $data['resource']->name }}</td>
                                                        <td>{{ $data['resource']->type }}</td>
                                                        <td>{{ $data['resource']->city->name ?? '-' }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $data['utilization_percentage'] > 80 ? 'bg-success' : ($data['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $data['utilization_percentage'] }}%"
                                                                     aria-valuenow="{{ $data['utilization_percentage'] }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $data['utilization_percentage'] }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $data['booked_days'] }}</td>
                                                        <td>{{ $data['total_days'] }}</td>
                                                        <td>{{ $data['bookings_count'] }}</td>
                                                        <td>${{ number_format($data['total_revenue'], 2) }}</td>
                                                        <td>
                                                            <a href="{{ route('dashboard.reports.resource-details', ['vehicle', $data['resource']->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                                                               class="btn btn-sm btn-primary">Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Guides Utilization -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6>Guides Utilization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Guide Name</th>
                                                    <th>City</th>
                                                    <th>Languages</th>
                                                    <th>Utilization %</th>
                                                    <th>Booked Days</th>
                                                    <th>Total Days</th>
                                                    <th>Bookings</th>
                                                    <th>Revenue</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($guideUtilization as $data)
                                                    <tr>
                                                        <td>{{ $data['resource']->name }}</td>
                                                        <td>{{ $data['resource']->city->name ?? '-' }}</td>
                                                        <td>{{ is_array($data['resource']->languages) ? implode(', ', $data['resource']->languages) : '-' }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $data['utilization_percentage'] > 80 ? 'bg-success' : ($data['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $data['utilization_percentage'] }}%"
                                                                     aria-valuenow="{{ $data['utilization_percentage'] }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $data['utilization_percentage'] }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $data['booked_days'] }}</td>
                                                        <td>{{ $data['total_days'] }}</td>
                                                        <td>{{ $data['bookings_count'] }}</td>
                                                        <td>${{ number_format($data['total_revenue'], 2) }}</td>
                                                        <td>
                                                            <a href="{{ route('dashboard.reports.resource-details', ['guide', $data['resource']->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                                                               class="btn btn-sm btn-primary">Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Representatives Utilization -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6>Representatives Utilization</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Representative Name</th>
                                                    <th>Company</th>
                                                    <th>City</th>
                                                    <th>Utilization %</th>
                                                    <th>Booked Days</th>
                                                    <th>Total Days</th>
                                                    <th>Bookings</th>
                                                    <th>Revenue</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($representativeUtilization as $data)
                                                    <tr>
                                                        <td>{{ $data['resource']->name }}</td>
                                                        <td>{{ $data['resource']->company_name ?? '-' }}</td>
                                                        <td>{{ $data['resource']->city->name ?? '-' }}</td>
                                                        <td>
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar {{ $data['utilization_percentage'] > 80 ? 'bg-success' : ($data['utilization_percentage'] > 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                                     role="progressbar" 
                                                                     style="width: {{ $data['utilization_percentage'] }}%"
                                                                     aria-valuenow="{{ $data['utilization_percentage'] }}" 
                                                                     aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $data['utilization_percentage'] }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{{ $data['booked_days'] }}</td>
                                                        <td>{{ $data['total_days'] }}</td>
                                                        <td>{{ $data['bookings_count'] }}</td>
                                                        <td>${{ number_format($data['total_revenue'], 2) }}</td>
                                                        <td>
                                                            <a href="{{ route('dashboard.reports.resource-details', ['representative', $data['resource']->id, 'start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                                                               class="btn btn-sm btn-primary">Details</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
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




