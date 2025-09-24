@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Reports Dashboard">
            <li class="breadcrumb-item active">Reports</li>
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
                    
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4>{{ $summary['inquiries']['total'] }}</h4>
                                            <p class="mb-0">Total Inquiries</p>
                                            <small>This Month: {{ $summary['inquiries']['this_month'] }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-envelope fa-2x"></i>
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
                                            <h4>{{ $summary['bookings']['total'] }}</h4>
                                            <p class="mb-0">Total Bookings</p>
                                            <small>This Month: {{ $summary['bookings']['this_month'] }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-file-text fa-2x"></i>
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
                                            <h4>${{ number_format($summary['payments']['total_amount'], 2) }}</h4>
                                            <p class="mb-0">Total Revenue</p>
                                            <small>This Month: ${{ number_format($summary['payments']['total_amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-dollar-sign fa-2x"></i>
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
                                            <h4>{{ $summary['clients']['total'] }}</h4>
                                            <p class="mb-0">Total Clients</p>
                                            <small>This Month: {{ $summary['clients']['this_month'] }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access Reports -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Quick Access Reports</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-envelope fa-3x text-primary mb-3"></i>
                                                    <h5>Inquiries Report</h5>
                                                    <p class="text-muted">View detailed inquiries analysis and conversion rates</p>
                                                    <a href="{{ route('dashboard.reports.inquiries') }}" class="btn btn-primary">
                                                        <i class="fa fa-chart-line"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-file-text fa-3x text-success mb-3"></i>
                                                    <h5>Bookings Report</h5>
                                                    <p class="text-muted">Analyze booking patterns and revenue trends</p>
                                                    <a href="{{ route('dashboard.reports.bookings') }}" class="btn btn-success">
                                                        <i class="fa fa-chart-bar"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-dollar-sign fa-3x text-warning mb-3"></i>
                                                    <h5>Finance Report</h5>
                                                    <p class="text-muted">Track payments, revenue, and financial performance</p>
                                                    <a href="{{ route('dashboard.reports.finance') }}" class="btn btn-warning">
                                                        <i class="fa fa-chart-pie"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-cogs fa-3x text-info mb-3"></i>
                                                    <h5>Operational Report</h5>
                                                    <p class="text-muted">Monitor resource utilization and operational efficiency</p>
                                                    <a href="{{ route('dashboard.reports.operational') }}" class="btn btn-info">
                                                        <i class="fa fa-chart-area"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-danger">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-trophy fa-3x text-danger mb-3"></i>
                                                    <h5>Performance Report</h5>
                                                    <p class="text-muted">Analyze KPIs and business performance metrics</p>
                                                    <a href="{{ route('dashboard.reports.performance') }}" class="btn btn-danger">
                                                        <i class="fa fa-chart-line"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-secondary">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-chart-bar fa-3x text-secondary mb-3"></i>
                                                    <h5>Resource Utilization</h5>
                                                    <p class="text-muted">Track hotel, vehicle, guide, and representative usage</p>
                                                    <a href="{{ route('dashboard.reports.resource-utilization') }}" class="btn btn-secondary">
                                                        <i class="fa fa-chart-bar"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-dark">
                                                <div class="card-body text-center">
                                                    <i class="fa fa-link fa-3x text-dark mb-3"></i>
                                                    <h5>Inquiry Resources</h5>
                                                    <p class="text-muted">Track resource assignments to inquiries and staff performance</p>
                                                    <a href="{{ route('dashboard.reports.inquiry-resources') }}" class="btn btn-dark">
                                                        <i class="fa fa-link"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Overview -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Inquiries Status Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-primary">{{ $summary['inquiries']['pending'] }}</h3>
                                                <p class="mb-0">Pending</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $summary['inquiries']['confirmed'] }}</h3>
                                                <p class="mb-0">Confirmed</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Bookings Status Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h3 class="text-warning">{{ $summary['bookings']['pending'] }}</h3>
                                                <p class="mb-0">Pending</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ $summary['bookings']['confirmed'] }}</h3>
                                                <p class="mb-0">Confirmed</p>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h3 class="text-success">{{ $summary['bookings']['completed'] }}</h3>
                                                <p class="mb-0">Completed</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Overview -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Payment Status Overview</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-success">${{ number_format($summary['payments']['paid_amount'], 2) }}</h3>
                                                <p class="mb-0">Paid Amount</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-warning">${{ number_format($summary['payments']['pending_amount'], 2) }}</h3>
                                                <p class="mb-0">Pending Amount</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-info">{{ $summary['payments']['total'] }}</h3>
                                                <p class="mb-0">Total Payments</p>
                                            </div>
                                        </div>
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
