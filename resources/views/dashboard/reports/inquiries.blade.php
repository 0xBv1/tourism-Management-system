@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Inquiries Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Inquiries</li>
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
                    
                    @if(auth()->user()->hasRole(['Reservation', 'Operator']))
                        <div class="alert alert-warning">
                            <i class="fa fa-filter"></i> 
                            <strong>Filtered Report:</strong> This report shows only inquiries assigned to you.
                        </div>
                    @endif
                    
                    <!-- Filter Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Filter Inquiries Report</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.inquiries') }}">
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
                                                <a href="{{ route('dashboard.reports.inquiries') }}" class="btn btn-secondary">Reset</a>
                                                <a href="{{ route('dashboard.reports.export', 'inquiries') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
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
                                            <h3 class="fw-bold">{{ $inquiries->count() }}</h3>
                                            <p class="mb-1 fs-6">Total Inquiries</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-envelope fa-2x"></i>
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
                                            <h3 class="fw-bold">{{ $conversionRate }}%</h3>
                                            <p class="mb-1 fs-6">Conversion Rate</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-percentage fa-2x"></i>
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
                                            <h3 class="fw-bold">{{ $inquiries->where('status', \App\Enums\InquiryStatus::PENDING)->count() }}</h3>
                                            <p class="mb-1 fs-6">Pending</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-clock fa-2x"></i>
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
                                            <h3 class="fw-bold">{{ $inquiries->where('status', \App\Enums\InquiryStatus::CONFIRMED)->count() }}</h3>
                                            <p class="mb-1 fs-6">Confirmed</p>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-check fa-2x"></i>
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
                                id="inquiry-status-chart"
                                type="doughnut"
                                :labels="array_column($statusData, 'label')"
                                :data="array_column($statusData, 'count')"
                                title="Inquiry Status Distribution"
                                subtitle="Breakdown of inquiries by status"
                                height="350px"
                                :colors="['#10b981', '#f59e0b', '#ef4444', '#8b5cf6']"
                                :statistics="[
                                    [
                                        'label' => 'Total Inquiries',
                                        'value' => $inquiries->count(),
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Conversion Rate',
                                        'value' => $conversionRate . '%',
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-advanced-chart 
                                id="inquiry-trend-chart"
                                type="line"
                                :labels="array_column($monthlyData, 'period')"
                                :datasets="[
                                    [
                                        'label' => 'Total Inquiries',
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
                                        'label' => 'Pending',
                                        'data' => array_column($monthlyData, 'pending'),
                                        'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                                        'borderColor' => '#f59e0b',
                                        'borderWidth' => 3,
                                        'fill' => true,
                                        'tension' => 0.4
                                    ]
                                ]"
                                title="Inquiry Trend Analysis"
                                subtitle="Detailed inquiry volume and status breakdown over time"
                                height="350px"
                                :gradient="true"
                                :animation="true"
                                :statistics="[
                                    [
                                        'label' => 'Avg Daily',
                                        'value' => isset($trendAnalysis['avg_daily_inquiries']) ? $trendAnalysis['avg_daily_inquiries'] : '0',
                                        'color' => 'info'
                                    ],
                                    [
                                        'label' => 'Peak Day',
                                        'value' => isset($trendAnalysis['peak_day']) ? $trendAnalysis['peak_day']['formatted_date'] . ' (' . $trendAnalysis['peak_day']['count'] . ')' : 'N/A',
                                        'color' => 'warning'
                                    ],
                                    [
                                        'label' => 'Growth Rate',
                                        'value' => isset($trendAnalysis['growth_rate']) ? $trendAnalysis['growth_rate'] . '%' : '0%',
                                        'color' => isset($trendAnalysis['growth_rate']) && $trendAnalysis['growth_rate'] >= 0 ? 'success' : 'danger'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Enhanced Top Clients Chart -->
                    @if($clientData->count() > 0)
                    <div class="row mt-3">
                        <div class="col-md-8">
                            <x-advanced-chart 
                                id="top-clients-chart"
                                type="bar"
                                :labels="array_column($clientData->take(10)->toArray(), 'client_name')"
                                :datasets="[
                                    [
                                        'label' => 'Total Inquiries',
                                        'data' => array_column($clientData->take(10)->toArray(), 'total_inquiries'),
                                        'backgroundColor' => 'rgba(139, 92, 246, 0.8)',
                                        'borderColor' => '#8b5cf6',
                                        'borderWidth' => 1
                                    ],
                                    [
                                        'label' => 'Confirmed',
                                        'data' => array_column($clientData->take(10)->toArray(), 'confirmed_inquiries'),
                                        'backgroundColor' => 'rgba(16, 185, 129, 0.8)',
                                        'borderColor' => '#10b981',
                                        'borderWidth' => 1
                                    ],
                                    [
                                        'label' => 'Pending',
                                        'data' => array_column($clientData->take(10)->toArray(), 'pending_inquiries'),
                                        'backgroundColor' => 'rgba(245, 158, 11, 0.8)',
                                        'borderColor' => '#f59e0b',
                                        'borderWidth' => 1
                                    ]
                                ]"
                                title="Top Clients by Inquiry Volume & Status"
                                subtitle="Most active clients with detailed status breakdown"
                                height="350px"
                                :gradient="true"
                                :animation="true"
                                :statistics="[
                                    [
                                        'label' => 'Top Client',
                                        'value' => $clientData->count() > 0 ? $clientData->first()['client_name'] : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Max Inquiries',
                                        'value' => $clientData->count() > 0 ? $clientData->first()['total_inquiries'] : 0,
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Best Conversion',
                                        'value' => $clientData->count() > 0 ? $clientData->sortByDesc('conversion_rate')->first()['conversion_rate'] . '%' : 'N/A',
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Client Insights
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-primary mb-1">{{ $clientData->count() }}</h4>
                                                <small class="text-muted">Active Clients</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-success mb-1">{{ $clientData->avg('conversion_rate') }}%</h4>
                                                <small class="text-muted">Avg Conversion</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-info mb-1">{{ $clientData->avg('avg_response_time') }}h</h4>
                                                <small class="text-muted">Avg Response Time</small>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="border rounded p-2">
                                                <h4 class="text-warning mb-1">{{ $clientData->sum('total_inquiries') }}</h4>
                                                <small class="text-muted">Total Inquiries</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6 class="mb-3">Top Performers</h6>
                                    @foreach($clientData->take(3) as $index => $client)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="badge bg-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'info') }}">
                                                #{{ $index + 1 }}
                                            </span>
                                            <strong>{{ Str::limit($client['client_name'], 15) }}</strong>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $client['total_inquiries'] }} inquiries</small><br>
                                            <small class="text-success">{{ $client['conversion_rate'] }}% conversion</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Analytics Charts -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="hourly-distribution-chart"
                                type="bar"
                                :labels="array_column($trendAnalysis['hourly_distribution'], 'label')"
                                :data="array_column($trendAnalysis['hourly_distribution'], 'count')"
                                title="Hourly Inquiry Distribution"
                                subtitle="Peak hours for inquiry submissions"
                                height="300px"
                                :colors="['#8b5cf6']"
                                :statistics="[
                                    [
                                        'label' => 'Peak Hour',
                                        'value' => isset($trendAnalysis['hourly_distribution']) && count($trendAnalysis['hourly_distribution']) > 0 ? collect($trendAnalysis['hourly_distribution'])->sortByDesc('count')->first()['label'] ?? 'N/A' : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Peak Count',
                                        'value' => isset($trendAnalysis['hourly_distribution']) && count($trendAnalysis['hourly_distribution']) > 0 ? collect($trendAnalysis['hourly_distribution'])->max('count') : 0,
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="conversion-trend-chart"
                                type="line"
                                :labels="array_column($monthlyData, 'period')"
                                :data="array_column($monthlyData, 'conversion_rate')"
                                title="Conversion Rate Trend"
                                subtitle="Conversion rate changes over time"
                                height="300px"
                                :colors="['#10b981']"
                                :statistics="[
                                    [
                                        'label' => 'Avg Conversion',
                                        'value' => count($monthlyData) > 0 ? round(array_sum(array_column($monthlyData, 'conversion_rate')) / count($monthlyData), 1) . '%' : '0%',
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Best Period',
                                        'value' => count($monthlyData) > 0 ? max(array_column($monthlyData, 'conversion_rate')) . '%' : '0%',
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Inquiries Table -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Inquiries Details ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped" id="inquiries-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Client</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($inquiries as $inquiry)
                                        <tr>
                                            <td>{{ $inquiry->id }}</td>
                                            <td>{{ $inquiry->name }}</td>
                                            <td>{{ $inquiry->email }}</td>
                                            <td>{{ $inquiry->phone }}</td>
                                            <td>{{ Str::limit($inquiry->subject, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $inquiry->status->getColor() }}">
                                                    {{ $inquiry->status->getLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $inquiry->client->name ?? 'N/A' }}</td>
                                            <td>{{ $inquiry->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No inquiries found for the selected period.</td>
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
    
    .badge {
        border-radius: 20px;
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endpush
