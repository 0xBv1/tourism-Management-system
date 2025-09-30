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
                            <i class="fa fa-user"></i> 
                            <strong>Current Role:</strong> {{ admin()->roles->pluck('name')->join(', ') }}
                        </div>
                    @endif
                    
                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-gradient-primary text-white shadow-lg">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3 class="fw-bold">{{ $summary['inquiries']['total'] }}</h3>
                                            <p class="mb-1 fs-6">Total Inquiries</p>
                                            <small class="opacity-75">This Month: {{ $summary['inquiries']['this_month'] }}</small>
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
                                            <h3 class="fw-bold">{{ $summary['bookings']['total'] }}</h3>
                                            <p class="mb-1 fs-6">Total Bookings</p>
                                            <small class="opacity-75">This Month: {{ $summary['bookings']['this_month'] }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-file-text fa-2x"></i>
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
                                            <h3 class="fw-bold">${{ number_format($summary['payments']['total_amount'], 2) }}</h3>
                                            <p class="mb-1 fs-6">Total Income</p>
                                            <small class="opacity-75">This Month: ${{ number_format($summary['payments']['total_amount'], 2) }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-dollar-sign fa-2x"></i>
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
                                            <h3 class="fw-bold">{{ $summary['clients']['total'] }}</h3>
                                            <p class="mb-1 fs-6">Total Clients</p>
                                            <small class="opacity-75">This Month: {{ $summary['clients']['this_month'] }}</small>
                                        </div>
                                        <div class="align-self-center">
                                            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                                <i class="fa fa-users fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access Reports -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="mb-0 fw-bold text-dark">
                                        <i class="fa fa-chart-line text-primary me-2"></i>
                                        Quick Access Reports
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-envelope fa-2x text-primary"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Inquiries Report</h5>
                                                    <p class="text-muted mb-3">View detailed inquiries analysis and conversion rates</p>
                                                    <a href="{{ route('dashboard.reports.inquiries') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-line me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-file-text fa-2x text-success"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Bookings Report</h5>
                                                    <p class="text-muted mb-3">Analyze booking patterns and income trends</p>
                                                    <a href="{{ route('dashboard.reports.bookings') }}" class="btn btn-success btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-bar me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-dollar-sign fa-2x text-warning"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Finance Report</h5>
                                                    <p class="text-muted mb-3">Track payments, income, and financial performance</p>
                                                    <a href="{{ route('dashboard.reports.finance') }}" class="btn btn-warning btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-pie me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-cogs fa-2x text-info"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Operational Report</h5>
                                                    <p class="text-muted mb-3">Monitor resource utilization and operational efficiency</p>
                                                    <a href="{{ route('dashboard.reports.operational') }}" class="btn btn-info btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-area me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-trophy fa-2x text-danger"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Performance Report</h5>
                                                    <p class="text-muted mb-3">Analyze KPIs and business performance metrics</p>
                                                    <a href="{{ route('dashboard.reports.performance') }}" class="btn btn-danger btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-line me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-chart-bar fa-2x text-secondary"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Resource Utilization</h5>
                                                    <p class="text-muted mb-3">Track hotel, vehicle, guide, and representative usage</p>
                                                    <a href="{{ route('dashboard.reports.resource-utilization') }}" class="btn btn-secondary btn-sm rounded-pill px-4">
                                                        <i class="fa fa-chart-bar me-1"></i> View Report
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="card h-100 border-0 shadow-sm hover-card">
                                                <div class="card-body text-center p-4">
                                                    <div class="bg-dark bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fa fa-link fa-2x text-dark"></i>
                                                    </div>
                                                    <h5 class="fw-bold mb-2">Inquiry Resources</h5>
                                                    <p class="text-muted mb-3">Track resource assignments to inquiries and staff performance</p>
                                                    <a href="{{ route('dashboard.reports.inquiry-resources') }}" class="btn btn-dark btn-sm rounded-pill px-4">
                                                        <i class="fa fa-link me-1"></i> View Report
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
    
    .hover-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
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
    
    .shadow-lg {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .shadow-sm {
        box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    }
</style>
@endpush
