@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Resource Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.resource-utilization') }}">Resource Reports</a></li>
            <li class="breadcrumb-item active">{{ ucfirst($resourceType) }} - {{ $resource->name }}</li>
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
                            <h5>{{ $resource->name }} - Utilization Details</h5>
                            <div class="card-header-right">
                                <form method="GET" class="d-inline-flex">
                                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                    <input type="date" name="start_date" class="form-control me-2" 
                                           value="{{ $startDate->format('Y-m-d') }}" required>
                                    <input type="date" name="end_date" class="form-control me-2" 
                                           value="{{ $endDate->format('Y-m-d') }}" required>
                                    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Resource Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Resource Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $resource->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>{{ ucfirst($resourceType) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $resource->city->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $resource->status_color }}">
                                                    {{ $resource->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($resourceType === 'hotel')
                                            <tr>
                                                <td><strong>Total Rooms:</strong></td>
                                                <td>{{ $resource->total_rooms }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Available Rooms:</strong></td>
                                                <td>{{ $resource->available_rooms }}</td>
                                            </tr>
                                        @elseif($resourceType === 'vehicle')
                                            <tr>
                                                <td><strong>Type:</strong></td>
                                                <td>{{ $resource->type }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Capacity:</strong></td>
                                                <td>{{ $resource->capacity }}</td>
                                            </tr>
                                        @elseif(in_array($resourceType, ['guide', 'representative']))
                                            <tr>
                                                <td><strong>Languages:</strong></td>
                                                <td>{{ is_array($resource->languages) ? implode(', ', $resource->languages) : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Experience:</strong></td>
                                                <td>{{ $resource->experience_years }} years</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Utilization Statistics</h6>
                                    <div class="text-center">
                                        <div class="utilization-circle mb-3">
                                            <div class="progress-circle" data-percentage="{{ $utilization['utilization_percentage'] }}">
                                                <span class="percentage">{{ $utilization['utilization_percentage'] }}%</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h4>{{ $utilization['booked_days'] }}</h4>
                                                    <p>Booked Days</p>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h4>{{ $utilization['total_days'] }}</h4>
                                                    <p>Total Days</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h4>{{ $utilization['bookings_count'] }}</h4>
                                                    <p>Bookings</p>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="stat-item">
                                                    <h4>${{ number_format($utilization['total_revenue'], 2) }}</h4>
                                                    <p>Revenue</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bookings Timeline -->
                            <div class="card">
                                <div class="card-header">
                                    <h6>Bookings Timeline ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h6>
                                </div>
                                <div class="card-body">
                                    @if($bookings->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Booking File</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Duration</th>
                                                        <th>Status</th>
                                                        <th>Total Price</th>
                                                        <th>Client</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bookings as $booking)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('dashboard.bookings.show', $booking->bookingFile) }}">
                                                                    {{ $booking->bookingFile->file_name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                                            <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                                            <td>{{ $booking->duration_in_days }} days</td>
                                                            <td>
                                                                <span class="badge badge-{{ $booking->status_color }}">
                                                                    {{ $booking->status_label }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $booking->currency }} {{ number_format($booking->total_price, 2) }}</td>
                                                            <td>
                                                                @if($booking->bookingFile->inquiry && $booking->bookingFile->inquiry->client)
                                                                    {{ $booking->bookingFile->inquiry->client->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <p class="text-muted">No bookings found for this period.</p>
                                        </div>
                                    @endif
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
.utilization-circle {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
}

.progress-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: conic-gradient(#007bff 0deg, #007bff calc(var(--percentage) * 3.6deg), #e9ecef calc(var(--percentage) * 3.6deg));
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.progress-circle::before {
    content: '';
    position: absolute;
    width: 120px;
    height: 120px;
    background: white;
    border-radius: 50%;
}

.percentage {
    position: relative;
    z-index: 1;
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.stat-item {
    text-align: center;
    padding: 10px;
}

.stat-item h4 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.stat-item p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-circle');
    const percentage = progressCircle.dataset.percentage;
    progressCircle.style.setProperty('--percentage', percentage);
});
</script>
@endpush




