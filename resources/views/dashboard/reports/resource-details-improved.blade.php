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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    <i class="fas fa-{{ $resourceType === 'hotel' ? 'hotel' : ($resourceType === 'vehicle' ? 'car' : ($resourceType === 'guide' ? 'user-tie' : 'handshake')) }} me-2"></i>
                                    {{ $resource->name }} - Detailed Analysis
                                </h5>
                                <small class="text-muted">{{ ucfirst($resourceType) }} utilization and booking details</small>
                            </div>
                            <div class="d-flex gap-2">
                                <form method="GET" class="d-flex gap-2">
                                    <input type="date" name="start_date" class="form-control form-control-sm" 
                                           value="{{ $startDate->format('Y-m-d') }}" required>
                                    <input type="date" name="end_date" class="form-control form-control-sm" 
                                           value="{{ $endDate->format('Y-m-d') }}" required>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter me-1"></i>Filter
                                    </button>
                                </form>
                                <div class="btn-group">
                                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                                        <i class="fas fa-print me-1"></i>Print
                                    </button>
                                    <button class="btn btn-outline-success btn-sm" onclick="exportToCSV()">
                                        <i class="fas fa-download me-1"></i>Export
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Enhanced Resource Information -->
                            <div class="row mb-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                                Resource Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td class="fw-bold text-muted">Name:</td>
                                                            <td>{{ $resource->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-bold text-muted">Type:</td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ ucfirst($resourceType) }}</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-bold text-muted">City:</td>
                                                            <td>
                                                                @if($resource->city)
                                                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                                                    {{ $resource->city->name }}
                                                                @else
                                                                    <span class="text-muted">Not specified</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="fw-bold text-muted">Status:</td>
                                                            <td>
                                                                <span class="badge bg-{{ $resource->status === 'active' ? 'success' : 'secondary' }}">
                                                                    <i class="fas fa-{{ $resource->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
                                                                    {{ ucfirst($resource->status ?? 'Active') }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm">
                                                        @if($resourceType === 'hotel')
                                                            <tr>
                                                                <td class="fw-bold text-muted">Star Rating:</td>
                                                                <td>
                                                                    @if($resource->star_rating)
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star {{ $i <= $resource->star_rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                        @endfor
                                                                        <small class="text-muted ms-1">({{ $resource->star_rating }}/5)</small>
                                                                    @else
                                                                        <span class="text-muted">Not rated</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Total Rooms:</td>
                                                                <td>{{ $resource->total_rooms ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Available Rooms:</td>
                                                                <td>{{ $resource->available_rooms ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Price/Night:</td>
                                                                <td>
                                                                    @if($resource->price_per_night)
                                                                        <span class="fw-bold text-success">
                                                                            {{ $resource->currency ?? '$' }} {{ number_format($resource->price_per_night, 2) }}
                                                                        </span>
                                                                    @else
                                                                        <span class="text-muted">Not set</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @elseif($resourceType === 'vehicle')
                                                            <tr>
                                                                <td class="fw-bold text-muted">Vehicle Type:</td>
                                                                <td>{{ $resource->type ?? 'Not specified' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Brand & Model:</td>
                                                                <td>{{ $resource->brand ?? 'N/A' }} {{ $resource->model ?? '' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Capacity:</td>
                                                                <td>
                                                                    @if($resource->capacity)
                                                                        <i class="fas fa-users me-1 text-info"></i>
                                                                        {{ $resource->capacity }} passengers
                                                                    @else
                                                                        <span class="text-muted">Not specified</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Pricing:</td>
                                                                <td>
                                                                    @if($resource->price_per_day || $resource->price_per_hour)
                                                                        <div class="small">
                                                                            @if($resource->price_per_day)
                                                                                <span class="badge bg-success me-1">
                                                                                    {{ $resource->currency ?? '$' }} {{ number_format($resource->price_per_day, 2) }}/day
                                                                                </span>
                                                                            @endif
                                                                            @if($resource->price_per_hour)
                                                                                <span class="badge bg-info">
                                                                                    {{ $resource->currency ?? '$' }} {{ number_format($resource->price_per_hour, 2) }}/hour
                                                                                </span>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">Not set</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @elseif(in_array($resourceType, ['guide', 'representative']))
                                                            <tr>
                                                                <td class="fw-bold text-muted">Languages:</td>
                                                                <td>
                                                                    @if($resource->languages && count($resource->languages) > 0)
                                                                        @foreach($resource->languages as $language)
                                                                            <span class="badge bg-info me-1">{{ $language }}</span>
                                                                        @endforeach
                                                                    @else
                                                                        <span class="text-muted">Not specified</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Experience:</td>
                                                                <td>
                                                                    @if($resource->experience_years)
                                                                        <i class="fas fa-medal me-1 text-warning"></i>
                                                                        {{ $resource->experience_years }} years
                                                                    @else
                                                                        <span class="text-muted">Not specified</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Rating:</td>
                                                                <td>
                                                                    @if($resource->rating)
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i class="fas fa-star {{ $i <= $resource->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                                        @endfor
                                                                        <small class="text-muted ms-1">({{ $resource->rating }}/5)</small>
                                                                    @else
                                                                        <span class="text-muted">Not rated</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="fw-bold text-muted">Contact:</td>
                                                                <td>
                                                                    @if($resource->phone || $resource->email)
                                                                        <div class="small">
                                                                            @if($resource->phone)
                                                                                <div><i class="fas fa-phone me-1 text-success"></i>{{ $resource->phone }}</div>
                                                                            @endif
                                                                            @if($resource->email)
                                                                                <div><i class="fas fa-envelope me-1 text-primary"></i>{{ $resource->email }}</div>
                                                                            @endif
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted">Not available</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-chart-pie me-2 text-success"></i>
                                                Utilization Statistics
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="utilization-circle mb-3">
                                                <div class="progress-circle" data-percentage="{{ $utilization['utilization_percentage'] }}">
                                                    <span class="percentage">{{ $utilization['utilization_percentage'] }}%</span>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <div class="stat-item">
                                                        <h4 class="text-primary">{{ $utilization['booked_days'] }}</h4>
                                                        <p class="small text-muted mb-0">Booked Days</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="stat-item">
                                                        <h4 class="text-info">{{ $utilization['total_days'] }}</h4>
                                                        <p class="small text-muted mb-0">Total Days</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="stat-item">
                                                        <h4 class="text-success">{{ $utilization['bookings_count'] }}</h4>
                                                        <p class="small text-muted mb-0">Bookings</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="stat-item">
                                                        <h4 class="text-warning">${{ number_format($utilization['total_revenue'], 0) }}</h4>
                                                        <p class="small text-muted mb-0">Revenue</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($utilization['bookings_count'] > 0)
                                                <hr class="my-3">
                                                <div class="row g-2 text-center">
                                                    <div class="col-12">
                                                        <div class="stat-item">
                                                            <h5 class="text-secondary mb-0">
                                                                ${{ number_format($utilization['total_revenue'] / $utilization['bookings_count'], 2) }}
                                                            </h5>
                                                            <p class="small text-muted mb-0">Avg. Booking Value</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Bookings Timeline -->
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                        Bookings Timeline
                                        <small class="text-muted ms-2">
                                            ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})
                                        </small>
                                    </h6>
                                    @if($bookings->count() > 0)
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary">{{ $bookings->count() }} bookings</span>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="toggleBookingDetails()">
                                                <i class="fas fa-eye me-1"></i>Toggle Details
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body">
                                    @if($bookings->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="bookingsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>
                                                            <i class="fas fa-file-alt me-1"></i>
                                                            Booking File
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-calendar-check me-1"></i>
                                                            Start Date
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-calendar-times me-1"></i>
                                                            End Date
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-clock me-1"></i>
                                                            Duration
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Status
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-dollar-sign me-1"></i>
                                                            Total Price
                                                        </th>
                                                        <th>
                                                            <i class="fas fa-user me-1"></i>
                                                            Client
                                                        </th>
                                                        <th class="booking-details-col" style="display: none;">
                                                            <i class="fas fa-list me-1"></i>
                                                            Details
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($bookings as $booking)
                                                        <tr class="booking-row">
                                                            <td>
                                                                <a href="{{ route('dashboard.bookings.show', $booking->bookingFile) }}" 
                                                                   class="text-decoration-none fw-medium">
                                                                    <i class="fas fa-external-link-alt me-1 text-primary"></i>
                                                                    {{ $booking->bookingFile->file_name }}
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium">{{ $booking->start_date->format('M d, Y') }}</span>
                                                                <br>
                                                                <small class="text-muted">{{ $booking->start_date->format('l') }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="fw-medium">{{ $booking->end_date->format('M d, Y') }}</span>
                                                                <br>
                                                                <small class="text-muted">{{ $booking->end_date->format('l') }}</small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-info">
                                                                    {{ $booking->duration_in_days }} day{{ $booking->duration_in_days > 1 ? 's' : '' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                                                    <i class="fas fa-{{ $booking->status === 'confirmed' ? 'check-circle' : ($booking->status === 'pending' ? 'clock' : 'times-circle') }} me-1"></i>
                                                                    {{ ucfirst($booking->status ?? 'Unknown') }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="fw-bold text-success">
                                                                    {{ $booking->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}
                                                                </span>
                                                                @if($booking->duration_in_days > 0)
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        ${{ number_format($booking->total_price / $booking->duration_in_days, 2) }}/day
                                                                    </small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($booking->bookingFile->inquiry && $booking->bookingFile->inquiry->client)
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="avatar-sm me-2">
                                                                            <div class="avatar-title rounded-circle bg-light text-primary">
                                                                                {{ substr($booking->bookingFile->inquiry->client->name, 0, 2) }}
                                                                            </div>
                                                                        </div>
                                                                        <div>
                                                                            <div class="fw-medium">{{ $booking->bookingFile->inquiry->client->name }}</div>
                                                                            @if($booking->bookingFile->inquiry->client->email)
                                                                                <small class="text-muted">{{ $booking->bookingFile->inquiry->client->email }}</small>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">No client info</span>
                                                                @endif
                                                            </td>
                                                            <td class="booking-details-col" style="display: none;">
                                                                <button class="btn btn-sm btn-outline-info" 
                                                                        onclick="showBookingDetails('{{ $booking->id }}')">
                                                                    <i class="fas fa-info-circle"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="5" class="fw-bold text-end">Totals:</td>
                                                        <td class="fw-bold text-success">
                                                            ${{ number_format($bookings->sum('total_price'), 2) }}
                                                        </td>
                                                        <td class="fw-bold">{{ $bookings->count() }} booking{{ $bookings->count() > 1 ? 's' : '' }}</td>
                                                        <td class="booking-details-col" style="display: none;"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h6 class="text-muted">No bookings found for this period</h6>
                                            <p class="text-muted">
                                                This {{ $resourceType }} has no bookings between 
                                                {{ $startDate->format('M d, Y') }} and {{ $endDate->format('M d, Y') }}.
                                            </p>
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
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.progress-circle {
    width: 120px;
    height: 120px;
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
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
}

.percentage {
    position: relative;
    z-index: 1;
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
}

.stat-item {
    text-align: center;
    padding: 8px;
}

.stat-item h4, .stat-item h5 {
    margin: 0;
    font-weight: bold;
}

.stat-item p {
    margin: 0;
    font-size: 12px;
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.booking-row:hover {
    background-color: #f8f9fa;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    font-size: 0.875rem;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

@media print {
    .btn, .form-control, .card-header .d-flex > div:last-child {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const percentage = progressCircle.dataset.percentage;
        progressCircle.style.setProperty('--percentage', percentage);
    }
});

function toggleBookingDetails() {
    const detailsCols = document.querySelectorAll('.booking-details-col');
    const isVisible = detailsCols[0].style.display !== 'none';
    
    detailsCols.forEach(col => {
        col.style.display = isVisible ? 'none' : 'table-cell';
    });
    
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    const text = btn.querySelector('.btn-text') || btn;
    
    if (isVisible) {
        icon.className = 'fas fa-eye me-1';
        if (text !== btn) text.textContent = 'Show Details';
    } else {
        icon.className = 'fas fa-eye-slash me-1';
        if (text !== btn) text.textContent = 'Hide Details';
    }
}

function showBookingDetails(bookingId) {
    // This would show detailed booking information in a modal
    alert('Booking details functionality would be implemented here for booking ID: ' + bookingId);
}

function exportToCSV() {
    const resourceName = '{{ $resource->name }}';
    const resourceType = '{{ $resourceType }}';
    const startDate = '{{ $startDate->format("Y-m-d") }}';
    const endDate = '{{ $endDate->format("Y-m-d") }}';
    
    let csvContent = `Resource Details Report\n`;
    csvContent += `Resource: ${resourceName} (${resourceType})\n`;
    csvContent += `Period: ${startDate} to ${endDate}\n`;
    csvContent += `Generated: ${new Date().toLocaleString()}\n\n`;
    
    csvContent += `Utilization Summary\n`;
    csvContent += `Utilization Percentage,{{ $utilization['utilization_percentage'] }}%\n`;
    csvContent += `Booked Days,{{ $utilization['booked_days'] }}\n`;
    csvContent += `Total Days,{{ $utilization['total_days'] }}\n`;
    csvContent += `Bookings Count,{{ $utilization['bookings_count'] }}\n`;
    csvContent += `Total Revenue,${{ number_format($utilization['total_revenue'], 2) }}\n\n`;
    
    @if($bookings->count() > 0)
    csvContent += `Booking Details\n`;
    csvContent += `Booking File,Start Date,End Date,Duration (Days),Status,Total Price,Client\n`;
    @foreach($bookings as $booking)
    csvContent += `"{{ $booking->bookingFile->file_name }}","{{ $booking->start_date->format('Y-m-d') }}","{{ $booking->end_date->format('Y-m-d') }}",{{ $booking->duration_in_days }},"{{ ucfirst($booking->status ?? 'Unknown') }}","{{ $booking->currency ?? '$' }} {{ number_format($booking->total_price, 2) }}","{{ $booking->bookingFile->inquiry && $booking->bookingFile->inquiry->client ? $booking->bookingFile->inquiry->client->name : 'No client info' }}"\n`;
    @endforeach
    @endif
    
    // Create and download CSV file
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `resource_details_${resourceName.replace(/\s+/g, '_')}_${startDate}_to_${endDate}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
