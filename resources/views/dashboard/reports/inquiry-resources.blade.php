@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Inquiry Resources Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Inquiry Resources</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Date Range Filter -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>Date Range Filter
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.inquiry-resources') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ $startDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ $endDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter me-1"></i>Apply Filter
                                        </button>
                                        <a href="{{ route('dashboard.reports.inquiry-resources') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-refresh me-1"></i>Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $inquiryResources->count() }}</h4>
                                            <p class="mb-0">Total Resource Assignments</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-link fa-2x"></i>
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
                                            <h4>{{ $resourceAssignmentRate }}%</h4>
                                            <p class="mb-0">Assignment Rate</p>
                                            <small>{{ $inquiriesWithResources }} of {{ $totalInquiries }} inquiries</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-percentage fa-2x"></i>
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
                                            <h4>{{ $topResources->count() }}</h4>
                                            <p class="mb-0">Unique Resources Used</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-star fa-2x"></i>
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
                                            <h4>{{ $staffPerformance->count() }}</h4>
                                            <p class="mb-0">Active Staff Members</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resource Type Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Resource Type Breakdown
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="resourceTypeChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-bar me-2"></i>Resource Type Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @foreach($resourceTypeData as $type => $data)
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <span class="badge bg-{{ $type === 'hotel' ? 'primary' : ($type === 'vehicle' ? 'success' : ($type === 'guide' ? 'info' : ($type === 'representative' ? 'warning' : 'secondary'))) }}">
                                                    {{ $data['label'] }}
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <strong>{{ $data['count'] }}</strong>
                                                <small class="text-muted">({{ $data['percentage'] }}%)</small>
                                            </div>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $type === 'hotel' ? 'primary' : ($type === 'vehicle' ? 'success' : ($type === 'guide' ? 'info' : ($type === 'representative' ? 'warning' : 'secondary'))) }}" 
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Resources and Staff Performance -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-trophy me-2"></i>Top Resources by Usage
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($topResources->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Resource</th>
                                                        <th>Type</th>
                                                        <th>Usage Count</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($topResources as $resource)
                                                        <tr>
                                                            <td>{{ $resource['resource_name'] }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $resource['resource_type'] === 'hotel' ? 'primary' : ($resource['resource_type'] === 'vehicle' ? 'success' : ($resource['resource_type'] === 'guide' ? 'info' : ($resource['resource_type'] === 'representative' ? 'warning' : 'secondary'))) }}">
                                                                    {{ ucfirst($resource['resource_type']) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $resource['count'] }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>No resource assignments found for the selected period.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-tie me-2"></i>Staff Performance
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($staffPerformance->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Staff Member</th>
                                                        <th>Assignments</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($staffPerformance as $staff)
                                                        <tr>
                                                            <td>{{ $staff['user_name'] }}</td>
                                                            <td>
                                                                <span class="badge bg-success">{{ $staff['count'] }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>No staff assignments found for the selected period.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Resource Assignments -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Detailed Resource Assignments
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($inquiryResources->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Inquiry ID</th>
                                                <th>Client</th>
                                                <th>Resource Type</th>
                                                <th>Resource Name</th>
                                                <th>Added By</th>
                                                <th>Date Added</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inquiryResources as $assignment)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('dashboard.inquiries.show', $assignment->inquiry_id) }}" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            #{{ $assignment->inquiry_id }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $assignment->inquiry->client->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $assignment->resource_type === 'hotel' ? 'primary' : ($assignment->resource_type === 'vehicle' ? 'success' : ($assignment->resource_type === 'guide' ? 'info' : ($assignment->resource_type === 'representative' ? 'warning' : 'secondary'))) }}">
                                                            {{ ucfirst($assignment->resource_type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $assignment->resource_name }}</td>
                                                    <td>{{ $assignment->addedBy->name ?? 'Unknown' }}</td>
                                                    <td>{{ $assignment->created_at->format('M d, Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                                    <h5>No Resource Assignments Found</h5>
                                    <p>No resources have been assigned to inquiries for the selected date range.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Resource Type Chart
    const ctx = document.getElementById('resourceTypeChart').getContext('2d');
    const resourceTypeData = @json($resourceTypeData);
    
    const labels = Object.keys(resourceTypeData).map(key => resourceTypeData[key].label);
    const data = Object.keys(resourceTypeData).map(key => resourceTypeData[key].count);
    const colors = ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#6c757d'];
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
