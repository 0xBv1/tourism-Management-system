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
                            <i class="fa fa-user-tag"></i> 
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
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $kpis['inquiry_to_booking_conversion'] }}%</h4>
                                    <p class="mb-0">Inquiry to Booking</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $kpis['booking_to_payment_conversion'] }}%</h4>
                                    <p class="mb-0">Booking to Payment</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($kpis['average_inquiry_value'], 2) }}</h4>
                                    <p class="mb-0">Avg Inquiry Value</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($kpis['average_booking_value'], 2) }}</h4>
                                    <p class="mb-0">Avg Booking Value</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h4>${{ number_format($kpis['revenue_per_inquiry'], 2) }}</h4>
                                    <p class="mb-0">Revenue per Inquiry</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h4>{{ $startDate->diffInDays($endDate) + 1 }}</h4>
                                    <p class="mb-0">Days Analyzed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conversion Funnel -->
                    <div class="row mt-3">
                        <div class="col-md-6">
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Trend Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="trendChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                                    <th>Total Revenue</th>
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
                                                    <td>{{ $client['client']->inquiries->count() }}</td>
                                                    <td>${{ number_format($client['revenue'] / max($client['client']->inquiries->count(), 1), 2) }}</td>
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

                    <!-- Performance Metrics Grid -->
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Inquiry Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="inquiryTrendChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Booking Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="bookingTrendChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Revenue Trends</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueTrendChart" width="400" height="200"></canvas>
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
    background: linear-gradient(90deg, #007bff, #0056b3);
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const trends = @json($trends);
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trends.inquiries_trend.map(item => item.month),
            datasets: [
                {
                    label: 'Inquiries',
                    data: trends.inquiries_trend.map(item => item.count),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Bookings',
                    data: trends.bookings_trend.map(item => item.count),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Inquiry Trend Chart
    const inquiryTrendCtx = document.getElementById('inquiryTrendChart').getContext('2d');
    new Chart(inquiryTrendCtx, {
        type: 'bar',
        data: {
            labels: trends.inquiries_trend.map(item => item.month),
            datasets: [{
                label: 'Inquiries',
                data: trends.inquiries_trend.map(item => item.count),
                backgroundColor: 'rgba(0, 123, 255, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Booking Trend Chart
    const bookingTrendCtx = document.getElementById('bookingTrendChart').getContext('2d');
    new Chart(bookingTrendCtx, {
        type: 'bar',
        data: {
            labels: trends.bookings_trend.map(item => item.month),
            datasets: [{
                label: 'Bookings',
                data: trends.bookings_trend.map(item => item.count),
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Revenue Trend Chart
    const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(revenueTrendCtx, {
        type: 'line',
        data: {
            labels: trends.revenue_trend.map(item => item.date),
            datasets: [{
                label: 'Daily Revenue',
                data: trends.revenue_trend.map(item => item.revenue),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
