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
                            <i class="fa fa-user"></i> 
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
                                    <div class="card bg-gradient-primary text-white shadow-lg">
                                        <div class="card-body text-center">
                                            <h3 class="fw-bold">{{ $overallStats['total_resources'] }}</h3>
                                            <p class="mb-0 fs-6">Total Resources</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-gradient-success text-white shadow-lg">
                                        <div class="card-body text-center">
                                            <h3 class="fw-bold">{{ $overallStats['total_bookings'] }}</h3>
                                            <p class="mb-0 fs-6">Total Bookings</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-gradient-info text-white shadow-lg">
                                        <div class="card-body text-center">
                                            <h3 class="fw-bold">${{ number_format($overallStats['total_revenue'], 2) }}</h3>
                                            <p class="mb-0 fs-6">Total Income</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-gradient-warning text-white shadow-lg">
                                        <div class="card-body text-center">
                                            <h3 class="fw-bold">{{ $overallStats['period_days'] }}</h3>
                                            <p class="mb-0 fs-6">Period Days</p>
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
    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
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
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
    
    .badge {
        border-radius: 20px;
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endpush




