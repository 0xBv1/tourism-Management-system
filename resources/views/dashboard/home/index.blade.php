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
                                           color="danger" :url="route('dashboard.users.index')"/>

            <x-dashboard.partials.box-card permission="countries.list" title="Countries" :count="\App\Models\Country::count()" icon="globe"
                                           color="primary" :url="Route::has('dashboard.countries.index') ? route('dashboard.countries.index') : null"/>

            <x-dashboard.partials.box-card permission="inquiries.list" title="Inquiries" :count="\App\Models\Inquiry::count()" icon="message-circle"
                                           color="success" :url="route('dashboard.inquiries.index')"/>

            <x-dashboard.partials.box-card permission="bookings.list" title="Bookings" :count="\App\Models\BookingFile::count()" icon="file-text"
                                           color="warning" :url="route('dashboard.bookings.index')"/>

            <x-dashboard.partials.box-card permission="hotels.list" title="Hotels" :count="\App\Models\Hotel::count()" icon="home"
                                           color="info" :url="route('dashboard.hotels.index')"/>

            <x-dashboard.partials.box-card permission="vehicles.list" title="Vehicles" :count="\App\Models\Vehicle::count()" icon="truck"
                                           color="secondary" :url="route('dashboard.vehicles.index')"/>

            <x-dashboard.partials.box-card permission="guides.list" title="Guides" :count="\App\Models\Guide::count()" icon="user"
                                           color="dark" :url="route('dashboard.guides.index')"/>

            <x-dashboard.partials.box-card permission="clients.list" title="Clients" :count="\App\Models\Client::count()" icon="users"
                                           color="primary" :url="Route::has('dashboard.clients.index') ? route('dashboard.clients.index') : null"/>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card recent-activity-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i data-feather="activity" class="mr-2 text-primary"></i>
                            Recent Activity
                        </h5>
                        <div class="activity-stats">
                            <span class="badge badge-primary">{{ \App\Models\Inquiry::count() }} Total</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-feed">
                            @php
                                $recentInquiries = \App\Models\Inquiry::with('client')->latest()->limit(5)->get();
                                $recentBookings = \App\Models\BookingFile::with('inquiry.client')->latest()->limit(5)->get();
                            @endphp
                            
                            @if($recentInquiries->count() > 0)
                                <div class="activity-section">
                                    <div class="section-header">
                                        <h6 class="section-title">
                                            <i data-feather="message-circle" class="mr-2"></i>
                                            Latest Inquiries
                                        </h6>
                                        <div class="section-indicator">
                                            <span class="indicator-dot"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="activity-timeline">
                                        @foreach($recentInquiries as $index => $inquiry)
                                            <div class="activity-item {{ $index === 0 ? 'latest' : '' }}" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                                                <div class="activity-icon">
                                                    <div class="icon-wrapper status-{{ $inquiry->status->value }}">
                                                        <i data-feather="{{ $inquiry->status->value === 'pending' ? 'clock' : ($inquiry->status->value === 'confirmed' ? 'check-circle' : 'x-circle') }}"></i>
                                                    </div>
                                                </div>
                                                <div class="activity-content">
                                                    <div class="activity-header">
                                                        <h6 class="activity-title">
                                                            <a href="{{ route('dashboard.inquiries.show', $inquiry->id) }}" class="activity-link">
                                                                {{ $inquiry->client->name ?? 'Unknown Client' }}
                                                            </a>
                                                        </h6>
                                                        <div class="activity-meta">
                                                            <span class="activity-time">
                                                                <i data-feather="clock" class="mr-1"></i>
                                                                {{ $inquiry->created_at->diffForHumans() }}
                                                            </span>
                                                            <span class="status-badge status-{{ $inquiry->status->value }}">
                                                                {{ ucfirst($inquiry->status->value) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="activity-description">
                                                        <p>{{ Str::limit($inquiry->description, 120) }}</p>
                                                    </div>
                                                    <div class="activity-footer">
                                                        <div class="activity-tags">
                                                            <span class="tag tag-client">
                                                                <i data-feather="user" class="mr-1"></i>
                                                                {{ $inquiry->client->email ?? 'No email' }}
                                                            </span>
                                                            @if($inquiry->created_at->isToday())
                                                                <span class="tag tag-today">
                                                                    <i data-feather="calendar" class="mr-1"></i>
                                                                    Today
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i data-feather="inbox" class="text-muted"></i>
                                    </div>
                                    <h6 class="empty-title">No Recent Activity</h6>
                                    <p class="empty-description">No inquiries have been created yet.</p>
                                    @if(admin()->can('inquiries.create'))
                                        <a href="{{ route('dashboard.inquiries.create') }}" class="btn btn-primary btn-sm">
                                            <i data-feather="plus" class="mr-1"></i>
                                            Create First Inquiry
                                        </a>
                                    @endif
                                </div>
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
    /* Recent Activity Card Enhancements */
    .recent-activity-card {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .recent-activity-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    
    .recent-activity-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }
    
    .recent-activity-card .card-header h5 {
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .recent-activity-card .card-header i {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .activity-stats .badge {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        font-weight: 500;
    }
    
    /* Activity Section */
    .activity-section {
        padding: 1.5rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }
    
    .section-title {
        color: #1e293b;
        font-weight: 600;
        font-size: 0.95rem;
        margin: 0;
        display: flex;
        align-items: center;
    }
    
    .section-title i {
        color: #3b82f6;
        width: 16px;
        height: 16px;
    }
    
    .indicator-dot {
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    /* Activity Timeline */
    .activity-timeline {
        position: relative;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #e2e8f0, #cbd5e1);
    }
    
    .activity-item {
        position: relative;
        margin-bottom: 1.5rem;
        padding-left: 3rem;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        transform: translateX(5px);
    }
    
    .activity-item.latest {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-radius: 12px;
        padding: 1rem 1rem 1rem 3rem;
        margin-left: -1rem;
        margin-right: -1rem;
        border: 1px solid #f59e0b;
    }
    
    .activity-item.latest::before {
        content: 'NEW';
        position: absolute;
        top: -8px;
        right: 1rem;
        background: #f59e0b;
        color: white;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .activity-icon {
        position: absolute;
        left: 0;
        top: 0.5rem;
    }
    
    .icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        border: 3px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .icon-wrapper.status-pending {
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: white;
    }
    
    .icon-wrapper.status-confirmed {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .icon-wrapper.status-cancelled {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
    
    .icon-wrapper i {
        width: 18px;
        height: 18px;
    }
    
    /* Activity Content */
    .activity-content {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .activity-item:hover .activity-content {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.75rem;
    }
    
    .activity-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
    
    .activity-link {
        color: #3b82f6;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .activity-link:hover {
        color: #1d4ed8;
        text-decoration: none;
    }
    
    .activity-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.5rem;
    }
    
    .activity-time {
        color: #64748b;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
    }
    
    .activity-time i {
        width: 12px;
        height: 12px;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }
    
    .status-badge.status-confirmed {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
    
    .status-badge.status-cancelled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    .activity-description {
        margin-bottom: 0.75rem;
    }
    
    .activity-description p {
        color: #475569;
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }
    
    .activity-footer {
        border-top: 1px solid #f1f5f9;
        padding-top: 0.75rem;
    }
    
    .activity-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .tag {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .tag i {
        width: 12px;
        height: 12px;
    }
    
    .tag-client {
        background: #e0e7ff;
        color: #3730a3;
    }
    
    .tag-today {
        background: #fef3c7;
        color: #92400e;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #64748b;
    }
    
    .empty-icon {
        margin-bottom: 1rem;
    }
    
    .empty-icon i {
        width: 48px;
        height: 48px;
        opacity: 0.5;
    }
    
    .empty-title {
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .empty-description {
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .activity-item {
            padding-left: 2.5rem;
        }
        
        .activity-timeline::before {
            left: 15px;
        }
        
        .icon-wrapper {
            width: 30px;
            height: 30px;
        }
        
        .icon-wrapper i {
            width: 14px;
            height: 14px;
        }
        
        .activity-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .activity-meta {
            align-items: flex-start;
            margin-top: 0.5rem;
        }
        
        .activity-tags {
            justify-content: flex-start;
        }
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
        
        // Enhanced Recent Activity Interactions
        initializeActivityFeed();
        
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
    
    function initializeActivityFeed() {
        // Add hover effects and animations to activity items
        const activityItems = document.querySelectorAll('.activity-item');
        
        activityItems.forEach(function(item, index) {
            // Staggered animation on load
            setTimeout(function() {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                item.style.transition = 'all 0.6s ease';
                
                setTimeout(function() {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 100);
            }, index * 150);
            
            // Add click tracking for activity links
            const activityLink = item.querySelector('.activity-link');
            if (activityLink) {
                activityLink.addEventListener('click', function(e) {
                    // Add ripple effect
                    createRippleEffect(e.target, e);
                    
                    // Track activity click
                    console.log('Activity item clicked:', this.textContent.trim());
                });
            }
            
            // Add hover sound effect (optional)
            item.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px) scale(1.02)';
            });
            
            item.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0) scale(1)';
            });
        });
        
        // Add real-time updates indicator
        addRealTimeIndicator();
        
        // Add activity filtering (if needed in future)
        addActivityFiltering();
    }
    
    function createRippleEffect(element, event) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(function() {
            ripple.remove();
        }, 600);
    }
    
    function addRealTimeIndicator() {
        const indicator = document.querySelector('.indicator-dot');
        if (indicator) {
            // Simulate real-time updates
            setInterval(function() {
                indicator.style.animation = 'none';
                setTimeout(function() {
                    indicator.style.animation = 'pulse 2s infinite';
                }, 10);
            }, 10000); // Every 10 seconds
        }
    }
    
    function addActivityFiltering() {
        // Add filter buttons for different activity types
        const activitySection = document.querySelector('.activity-section');
        if (activitySection && activitySection.querySelector('.activity-timeline')) {
            const sectionHeader = activitySection.querySelector('.section-header');
            
            // Create filter buttons
            const filterContainer = document.createElement('div');
            filterContainer.className = 'activity-filters';
            filterContainer.innerHTML = `
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="pending">Pending</button>
                    <button class="filter-btn" data-filter="confirmed">Confirmed</button>
                    <button class="filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
            `;
            
            sectionHeader.appendChild(filterContainer);
            
            // Add filter functionality
            const filterButtons = filterContainer.querySelectorAll('.filter-btn');
            const activityItems = document.querySelectorAll('.activity-item');
            
            filterButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    // Update active state
                    filterButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Filter activities
                    const filter = this.dataset.filter;
                    activityItems.forEach(function(item) {
                        if (filter === 'all') {
                            item.style.display = 'block';
                        } else {
                            const status = item.querySelector('.status-badge').classList.contains('status-' + filter);
                            item.style.display = status ? 'block' : 'none';
                        }
                    });
                });
            });
        }
    }
    
    // Add CSS for ripple effect and filter buttons
    const style = document.createElement('style');
    style.textContent = `
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .activity-filters {
            margin-left: auto;
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-btn {
            padding: 0.25rem 0.75rem;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        
        .filter-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        @media (max-width: 768px) {
            .filter-buttons {
                flex-wrap: wrap;
            }
            
            .filter-btn {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
        }
    `;
    document.head.appendChild(style);
</script>
@endpush