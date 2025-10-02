@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Performance Report">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.reports.index') }}">Reports</a></li>
            <li class="breadcrumb-item active">Performance</li>
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
                            <h5>Filter Performance Report</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('dashboard.reports.performance') }}">
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
                                                <a href="{{ route('dashboard.reports.performance') }}" class="btn btn-secondary">Reset</a>
                                                <a href="{{ route('dashboard.reports.export', 'performance') }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-success">
                                                    <i class="fa fa-download"></i> Export
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- KPI Cards -->
                    <div class="row mt-3">
                        <div class="col-md-2">
                            <div class="card bg-gradient-primary text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">{{ $kpis['inquiry_to_booking_conversion'] }}%</h4>
                                    <p class="mb-0 fs-6">Inquiry to Booking</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-success text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">{{ $kpis['booking_to_payment_conversion'] }}%</h4>
                                    <p class="mb-0 fs-6">Booking to Payment</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-warning text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">${{ number_format($kpis['average_inquiry_value'], 2) }}</h4>
                                    <p class="mb-0 fs-6">Avg Inquiry Value</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-info text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">${{ number_format($kpis['average_booking_value'], 2) }}</h4>
                                    <p class="mb-0 fs-6">Avg Booking Value</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-danger text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">${{ number_format($kpis['revenue_per_inquiry'], 2) }}</h4>
                                    <p class="mb-0 fs-6">Income per Inquiry</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-gradient-secondary text-black shadow-lg">
                                <div class="card-body text-center">
                                    <h4 class="fw-bold">{{ $startDate->diffInDays($endDate) + 1 }}</h4>
                                    <p class="mb-0 fs-6">Days Analyzed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversion Funnel -->
                    <!-- <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Conversion Funnel</h5>
                                </div>
                                <div class="card-body">
                                    <div class="funnel-container">
                                        @foreach($conversionFunnel as $stage)
                                        <div class="funnel-stage">
                                            <div class="funnel-bar" style="width: {{ max($stage['percentage'], 5) }}%">
                                                <div class="funnel-content">
                                                    <div class="funnel-text">
                                                        <div class="funnel-title">{{ $stage['stage'] }}</div>
                                                        <div class="funnel-stats">{{ $stage['count'] }} ({{ $stage['percentage'] }}%)</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Charts Row -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="conversion-funnel-chart"
                                type="bar"
                                :labels="array_column($conversionFunnel, 'stage')"
                                :data="array_column($conversionFunnel, 'count')"
                                title="Conversion Funnel"
                                subtitle="Sales funnel analysis"
                                height="350px"
                                :colors="['#8b5cf6', '#06b6d4', '#10b981', '#f59e0b']"
                                :statistics="[
                                    [
                                        'label' => 'Overall Conversion',
                                        'value' => $kpis['inquiry_to_booking_conversion'] . '%',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Payment Conversion',
                                        'value' => $kpis['booking_to_payment_conversion'] . '%',
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                        <div class="col-md-6">
                            <x-dashboard-chart 
                                id="revenue-trend-chart"
                                type="line"
                                :labels="array_column($trendAnalysis['revenue_trend'], 'date')"
                                :data="array_column($trendAnalysis['revenue_trend'], 'revenue')"
                                title="Revenue Trend"
                                subtitle="Revenue over time"
                                height="350px"
                                :colors="['#10b981']"
                                :statistics="[
                                    [
                                        'label' => 'Total Revenue',
                                        'value' => '$' . number_format(array_sum(array_column($trendAnalysis['revenue_trend'], 'revenue')), 2),
                                        'color' => 'success'
                                    ],
                                    [
                                        'label' => 'Avg Daily',
                                        'value' => '$' . number_format(array_sum(array_column($trendAnalysis['revenue_trend'], 'revenue')) / max(count($trendAnalysis['revenue_trend']), 1), 2),
                                        'color' => 'info'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>

                    <!-- Top Performers Chart -->
                    @if(isset($topPerformers['top_clients']) && count($topPerformers['top_clients']) > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <x-dashboard-chart 
                                id="top-clients-performance-chart"
                                type="bar"
                                :labels="array_column($topPerformers['top_clients'], 'client.name')"
                                :data="array_column($topPerformers['top_clients'], 'revenue')"
                                title="Top Clients by Revenue"
                                subtitle="Highest performing clients"
                                height="300px"
                                :colors="['#8b5cf6']"
                                :statistics="[
                                    [
                                        'label' => 'Top Client',
                                        'value' => count($topPerformers['top_clients']) > 0 ? $topPerformers['top_clients'][0]['client']->name : 'N/A',
                                        'color' => 'primary'
                                    ],
                                    [
                                        'label' => 'Max Revenue',
                                        'value' => '$' . number_format(count($topPerformers['top_clients']) > 0 ? $topPerformers['top_clients'][0]['revenue'] : 0, 2),
                                        'color' => 'success'
                                    ]
                                ]"
                                :exportable="true" />
                        </div>
                    </div>
                    @endif

                    <!-- Top Performers -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Top Performing Clients</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Rank</th>
                                                    <th>Client Name</th>
                                                    <th>Total Income</th>
                                                    <th>Bookings</th>
                                                    <th>Avg Booking Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($topPerformers['top_clients'] as $index => $client)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                                            #{{ $index + 1 }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $client['client']->name }}</td>
                                                    <td>${{ number_format($client['revenue'], 2) }}</td>
                                                    <td>{{ $client['inquiry_count'] }}</td>
                                                    <td>${{ number_format($client['revenue'] / max($client['inquiry_count'], 1), 2) }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No client performance data available.</td>
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
    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
    }
    .bg-gradient-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    .bg-gradient-pink {
        background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
    }
    .bg-gradient-indigo {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
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
    
    .funnel-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        padding: 20px;
    }

    .funnel-stage {
        position: relative;
        height: 60px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .funnel-bar {
        height: 100%;


        background: linear-gradient(90deg, #8b5cf6, #7c3aed);
        transition: width 0.3s ease;
        position: relative;
        min-width: 20px;
    }

    .funnel-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        z-index: 10;
    }

    .funnel-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    .funnel-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 4px;
        text-align: center;
    }

    .funnel-stats {
        font-size: 12px;
        opacity: 0.9;
        text-align: center;
    }

    /* Ensure text doesn't break into individual characters */
    .funnel-stage {
        word-wrap: normal;
        word-break: normal;
        white-space: normal;
    }

    .funnel-content {
        word-wrap: normal;
        word-break: normal;
        white-space: normal;
        line-height: 1.2;
    }

    /* Fallback for very small percentages */
    .funnel-bar[style*="width: 0%"] {
        min-width: 80px;
    }

    .funnel-bar[style*="width: 1%"],
    .funnel-bar[style*="width: 2%"],
    .funnel-bar[style*="width: 3%"],
    .funnel-bar[style*="width: 4%"],
    .funnel-bar[style*="width: 5%"] {
        min-width: 100px;
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js is no longer needed since trend charts are removed -->
@endpush
