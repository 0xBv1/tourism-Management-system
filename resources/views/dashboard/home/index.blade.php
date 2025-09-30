@extends('layouts.dashboard.app')

@section('content')

    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Dashboard">
            <li class="breadcrumb-item active">Dashboard</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Cards -->
        <div class="row">
            <x-dashboard.partials.box-card permission="users.list" title="Users" :count="\App\Models\User::count()" icon="users"
                                           color="danger"/>

            <x-dashboard.partials.box-card permission="countries.list" title="Countries" :count="\App\Models\Country::count()" icon="globe"
                                           color="primary"/>

            <x-dashboard.partials.box-card permission="inquiries.list" title="Inquiries" :count="\App\Models\Inquiry::count()" icon="message-circle"
                                           color="success"/>

            <x-dashboard.partials.box-card permission="bookings.list" title="Bookings" :count="\App\Models\BookingFile::count()" icon="file-text"
                                           color="warning"/>

            <x-dashboard.partials.box-card permission="hotels.list" title="Hotels" :count="\App\Models\Hotel::count()" icon="home"
                                           color="info"/>

            <x-dashboard.partials.box-card permission="vehicles.list" title="Vehicles" :count="\App\Models\Vehicle::count()" icon="truck"
                                           color="secondary"/>

            <x-dashboard.partials.box-card permission="guides.list" title="Guides" :count="\App\Models\Guide::count()" icon="user"
                                           color="dark"/>

            <x-dashboard.partials.box-card permission="clients.list" title="Clients" :count="\App\Models\Client::count()" icon="users"
                                           color="primary"/>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if(admin()->can('inquiries.create'))
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('dashboard.inquiries.create') }}" class="btn btn-primary btn-block">
                                    <i data-feather="plus-circle" class="mr-2"></i>New Inquiry
                                </a>
                            </div>
                            @endif
                            @if(admin()->can('bookings.list'))
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('dashboard.bookings.index') }}" class="btn btn-success btn-block">
                                    <i data-feather="file-text" class="mr-2"></i>View Bookings
                                </a>
                            </div>
                            @endif
                            @if(admin()->can('hotels.list'))
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('dashboard.hotels.index') }}" class="btn btn-info btn-block">
                                    <i data-feather="home" class="mr-2"></i>Manage Hotels
                                </a>
                            </div>
                            @endif
                            @if(admin()->can('reports.index'))
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="{{ route('dashboard.reports.index') }}" class="btn btn-warning btn-block">
                                    <i data-feather="bar-chart-2" class="mr-2"></i>View Reports
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="activity-feed">
                            @php
                                $recentInquiries = \App\Models\Inquiry::with('client')->latest()->limit(5)->get();
                                $recentBookings = \App\Models\BookingFile::with('inquiry.client')->latest()->limit(5)->get();
                            @endphp
                            
                            @if($recentInquiries->count() > 0)
                                <h6 class="text-primary mb-3">Latest Inquiries</h6>
                                @foreach($recentInquiries as $inquiry)
                                    <div class="media mb-3">
                                        <div class="media-body">
                                            <h6 class="mt-0 mb-1">
                                                <a href="{{ route('dashboard.inquiries.show', $inquiry->id) }}">
                                                    {{ $inquiry->client->name ?? 'Unknown Client' }}
                                                </a>
                                            </h6>
                                            <p class="mb-1">{{ Str::limit($inquiry->description, 100) }}</p>
                                            <small class="text-muted">
                                                <i data-feather="clock" class="mr-1"></i>
                                                {{ $inquiry->created_at->diffForHumans() }}
                                                <span class="badge badge-{{ $inquiry->status->value === 'pending' ? 'warning' : ($inquiry->status->value === 'confirmed' ? 'success' : 'secondary') }} ml-2">
                                                    {{ ucfirst($inquiry->status->value) }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No recent inquiries found.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5>System Status</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $pendingInquiries = \App\Models\Inquiry::where('status', 'pending')->count();
                            $confirmedInquiries = \App\Models\Inquiry::where('status', 'confirmed')->count();
                            $pendingBookings = \App\Models\BookingFile::where('status', 'pending')->count();
                            $activeHotels = \App\Models\Hotel::where('active', true)->count();
                            $activeVehicles = \App\Models\Vehicle::where('active', true)->count();
                            $activeGuides = \App\Models\Guide::where('active', true)->count();
                        @endphp
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ $pendingInquiries }}</h4>
                                    <p class="mb-0">Pending Inquiries</p>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $confirmedInquiries }}</h4>
                                    <p class="mb-0">Confirmed Inquiries</p>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <h4 class="text-info">{{ $pendingBookings }}</h4>
                                    <p class="mb-0">Pending Bookings</p>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ $activeHotels + $activeVehicles + $activeGuides }}</h4>
                                    <p class="mb-0">Active Resources</p>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="resource-status">
                            <h6 class="text-primary mb-3">Resource Availability</h6>
                            <div class="row">
                                <div class="col-4 text-center">
                                    <div class="resource-item">
                                        <i data-feather="home" class="text-info"></i>
                                        <p class="mb-0 mt-1">{{ $activeHotels }} Hotels</p>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="resource-item">
                                        <i data-feather="truck" class="text-secondary"></i>
                                        <p class="mb-0 mt-1">{{ $activeVehicles }} Vehicles</p>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="resource-item">
                                        <i data-feather="user" class="text-dark"></i>
                                        <p class="mb-0 mt-1">{{ $activeGuides }} Guides</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tourism Management System Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-primary mb-3">Welcome to Your Tourism Management System</h6>
                                <p class="mb-3">
                                    This comprehensive tourism management system provides complete control over your tourism operations, 
                                    including inquiry management, booking processing, resource allocation, and detailed reporting.
                                </p>
                                
                                <div class="features-list">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Inquiry Management:</strong> Track and manage customer inquiries
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Booking Processing:</strong> Automated booking file generation
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Resource Management:</strong> Hotels, vehicles, guides, and representatives
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Payment Tracking:</strong> Monitor payments and financial status
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Real-time Chat:</strong> Communicate with clients instantly
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Calendar Integration:</strong> Visual resource scheduling
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Comprehensive Reports:</strong> Detailed analytics and insights
                                                </li>
                                                <li class="mb-2">
                                                    <i data-feather="check-circle" class="text-success mr-2"></i>
                                                    <strong>Role-based Access:</strong> Secure permission management
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="system-info">
                                        <i data-feather="zap" class="text-warning" style="width: 48px; height: 48px;"></i>
                                        <h5 class="mt-3 mb-2">System Performance</h5>
                                        <p class="text-muted">All systems operational</p>
                                        
                                        <div class="mt-4">
                                            <small class="text-muted">Last updated: {{ now()->format('M d, Y H:i') }}</small>
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
@endsection

@push('styles')
<style>
    .activity-feed .media {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
    }
    
    .activity-feed .media:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .resource-item {
        padding: 15px;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    
    .resource-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }
    
    .resource-item i {
        width: 24px;
        height: 24px;
    }
    
    .features-list li {
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .features-list li:last-child {
        border-bottom: none;
    }
    
    .features-list i {
        width: 16px;
        height: 16px;
    }
    
    .btn-block {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-block i {
        width: 18px;
        height: 18px;
    }
    
    .system-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 30px 20px;
        border-radius: 12px;
        border: 1px solid #dee2e6;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.25em 0.5em;
    }
    
    /* Fix text visibility in colored card boxes */
    .success-box .media-body span,
    .success-box .media-body h3,
    .success-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .danger-box .media-body span,
    .danger-box .media-body h3,
    .danger-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .primary-box .media-body span,
    .primary-box .media-body h3,
    .primary-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .warning-box .media-body span,
    .warning-box .media-body h3,
    .warning-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .info-box .media-body span,
    .info-box .media-body h3,
    .info-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .secondary-box .media-body span,
    .secondary-box .media-body h3,
    .secondary-box .media-body h3 span {
        color: #333 !important;
        font-weight: 600;
    }
    
    .dark-box .media-body span,
    .dark-box .media-body h3,
    .dark-box .media-body h3 span {
        color: white !important;
        font-weight: 600;
    }
    
    /* Ensure widget icons are visible */
    .success-box .icons-widgets i,
    .danger-box .icons-widgets i,
    .primary-box .icons-widgets i,
    .warning-box .icons-widgets i,
    .info-box .icons-widgets i,
    .secondary-box .icons-widgets i {
        color: rgba(0,0,0,0.7) !important;
    }
    
    .dark-box .icons-widgets i {
        color: rgba(255,255,255,0.9) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Add click tracking for quick actions
        document.querySelectorAll('.btn-block').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // You can add analytics tracking here
                console.log('Quick action clicked:', this.textContent.trim());
            });
        });
        
        // Auto-refresh dashboard data every 5 minutes
        setInterval(function() {
            // You can implement AJAX refresh here if needed
            console.log('Dashboard auto-refresh triggered');
        }, 300000); // 5 minutes
    });
</script>
@endpush