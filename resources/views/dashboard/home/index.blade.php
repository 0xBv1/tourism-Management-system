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

            <x-dashboard.partials.box-card permission="tours.list" title="Tours" :count="\App\Models\Tour::count()" icon="grid"
                                           color="secondary"/>

            <x-dashboard.partials.box-card permission="destinations.list" title="Destinations" :count="\App\Models\Destination::count()" icon="globe"
                                           color="warning"/>

            <x-dashboard.partials.box-card permission="categories.list" title="Categories" :count="\App\Models\Category::count()" icon="book-open"
                                           color="primary"/>

            <x-dashboard.partials.box-card permission="tour-options.list" title="Tour Options" :count="\App\Models\TourOption::count()"
                                           icon="check-square" color="primary"/>

            <x-dashboard.partials.box-card permission="bookings.list" title="New Bookings"
                                           :count="\App\Models\Booking::whereDate('created_at', today())->count()"
                                           icon="edit-3" color="warning"/>

            <x-dashboard.partials.box-card permission="car-rentals.list" title="New Car Rentals"
                                           :count="\App\Models\CarRental::whereDate('created_at', today())->count()"
                                           icon="activity" color="secondary"/>

            <x-dashboard.partials.box-card permission="custom-trips.list" title="New Custom Trips"
                                           :count="\App\Models\CustomTrip::whereDate('created_at', today())->count()"
                                           icon="command" color="danger"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Services"
                                           :count="\App\Models\SupplierHotel::count() + \App\Models\SupplierTour::count() + \App\Models\SupplierTrip::count() + \App\Models\SupplierTransport::count() + \App\Models\SupplierRoom::count()"
                                           icon="briefcase" color="info"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Hotels"
                                           :count="\App\Models\SupplierHotel::count()"
                                           icon="home" color="success"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Rooms"
                                           :count="\App\Models\SupplierRoom::count()"
                                           icon="bed" color="primary"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Tours"
                                           :count="\App\Models\SupplierTour::count()"
                                           icon="map" color="warning"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Trips"
                                           :count="\App\Models\SupplierTrip::count()"
                                           icon="truck" color="secondary"/>

            <x-dashboard.partials.box-card permission="supplier-services.list" title="Supplier Transports"
                                           :count="\App\Models\SupplierTransport::count()"
                                           icon="car" color="dark"/>

            <x-dashboard.partials.box-card permission="service-approvals.list" title="Pending Approvals"
                                           :count="\App\Models\ServiceApproval::where('status', 'pending')->count()"
                                           icon="clock" color="warning"/>

            <x-dashboard.partials.box-card permission="service-approvals.list" title="Approved Services"
                                           :count="\App\Models\ServiceApproval::where('status', 'approved')->count()"
                                           icon="check-circle" color="secondary"/>

            <x-dashboard.partials.box-card permission="service-approvals.list" title="Rejected Services"
                                           :count="\App\Models\SupplierHotel::where('approved', false)->whereNotNull('rejection_reason')->count() + \App\Models\SupplierTour::where('approved', false)->whereNotNull('rejection_reason')->count() + \App\Models\SupplierTrip::where('approved', false)->whereNotNull('rejection_reason')->count() + \App\Models\SupplierTransport::where('approved', false)->whereNotNull('rejection_reason')->count() + \App\Models\SupplierRoom::where('approved', false)->whereNotNull('rejection_reason')->count()"
                                           icon="x-circle" color="danger"/>
        </div>

        <!-- Tables -->
        <div class="row">
            @canany(['bookings.index' , 'bookings.show'])
                <div class="col-xl-6 xl-100">
                    <div class="card">
                        <div class="card-header">
                            <h5>Latest Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="user-status table-responsive latest-order-table">
                                <table class="table table-bordernone">
                                    <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Created At</th>
                                        @can('bookings.show')
                                            <th scope="col"></th>
                                        @endcan
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse(\App\Models\Booking::latest('id')->limit(5)->get() as $booking)
                                        <tr>
                                            <td>{{  $booking->first_name . ' ' . $booking->last_name }}</td>
                                            <td class="digits">{{ $booking->currency?->symbol }}{{ number_format($booking->total_price*$booking->currency_exchange_rate, 2) }}</td>
                                            <td class="digits">{{ $booking->created_at->format('M Y, d') }}</td>
                                            @can('bookings.show')
                                                <td class="digits"><a
                                                        href="{{ route('dashboard.bookings.show', $booking) }}">View</a>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No Bookings Found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <a href="{{ route('dashboard.bookings.index') }}" class="btn btn-primary mt-4">View All
                                    Bookings</a>
                            </div>

                        </div>
                    </div>
                </div>
            @endcanany



            @canany(['car-rentals.index' , 'car-rentals.show'])
                <div class="col-xl-6 xl-100">
                    <div class="card">
                        <div class="card-header">
                            <h5>Latest Rentals</h5>
                        </div>
                        <div class="card-body">
                            <div class="user-status table-responsive latest-order-table">
                                <table class="table table-bordernone">
                                    <thead>
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Created At</th>
                                        @can('car-rentals.show')
                                            <th scope="col"></th>
                                        @endcan
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse(\App\Models\CarRental::latest('id')->limit(5)->get() as $carRental)
                                        <tr>
                                            <td>{{  $carRental->name }}</td>
                                            <td class="digits">
                                                {{ $carRental->currency?->symbol }}{{ number_format($carRental->currency_exchange_rate * ($carRental->car_route_price + $carRental->stops->sum('price')) ,2) }}
                                            </td>
                                            <td class="digits">{{ $carRental->created_at->format('M Y, d') }}</td>
                                            @can('car-rentals.show')
                                                <td class="digits">
                                                    <a href="{{ route('dashboard.car-rentals.show', $carRental) }}">View</a>
                                                </td>
                                            @endcan
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">No Rentals Found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <a href="{{ route('dashboard.car-rentals.index') }}" class="btn btn-primary mt-4">View All Rentals</a>
                            </div>

                        </div>
                    </div>
                </div>
            @endcanany


        </div>

        <!-- Supplier Services Tables -->
        <div class="row">
            @can('supplier-services.list')
                <div class="col-xl-6 xl-100">
                    <div class="card">
                        <div class="card-header">
                            <h5>Latest Supplier Services</h5>
                        </div>
                        <div class="card-body">
                            <div class="user-status table-responsive latest-order-table">
                                <table class="table table-bordernone">
                                    <thead>
                                    <tr>
                                        <th scope="col">Service</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Supplier</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $latestServices = collect();
                                        $latestServices = $latestServices->concat(\App\Models\SupplierHotel::with('supplier.user')->latest()->take(3)->get()->map(function($hotel) {
                                            $hotel->service_type = 'Hotel';
                                            $hotel->service_name = $hotel->name ?? 'N/A';
                                            return $hotel;
                                        }));
                                        $latestServices = $latestServices->concat(\App\Models\SupplierTour::with('supplier.user')->latest()->take(2)->get()->map(function($tour) {
                                            $tour->service_type = 'Tour';
                                            $tour->service_name = $tour->title ?? 'N/A';
                                            return $tour;
                                        }));
                                        $latestServices = $latestServices->concat(\App\Models\SupplierRoom::with('supplierHotel.supplier.user')->latest()->take(2)->get()->map(function($room) {
                                            $room->service_type = 'Room';
                                            $room->service_name = $room->name ?? 'N/A';
                                            return $room;
                                        }));
                                        $latestServices = $latestServices->sortByDesc('created_at')->take(5);
                                    @endphp

                                    @forelse($latestServices as $service)
                                        <tr>
                                            <td>{{ $service->service_name }}</td>
                                            <td><span class="badge bg-info">{{ $service->service_type }}</span></td>
                                            <td>{{ $service->supplier->user->name ?? ($service->supplierHotel->supplier->user->name ?? 'N/A') }}</td>
                                            <td>
                                                @if($service->approved)
                                                    <span class="badge bg-success">Approved</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="digits">{{ $service->created_at->format('M Y, d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">No Supplier Services Found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <a href="{{ route('dashboard.supplier-services.index') }}" class="btn btn-primary mt-4">View All Services</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan

            @can('service-approvals.list')
                <div class="col-xl-6 xl-100">
                    <div class="card">
                        <div class="card-header">
                            <h5>Latest Service Approvals</h5>
                        </div>
                        <div class="card-body">
                            <div class="user-status table-responsive latest-order-table">
                                <table class="table table-bordernone">
                                    <thead>
                                    <tr>
                                        <th scope="col">Service</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Supplier</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @forelse(\App\Models\ServiceApproval::with('supplier.user')->latest()->limit(5)->get() as $approval)
                                        <tr>
                                            <td>
                                                @php
                                                    $serviceName = 'N/A';
                                                    switch($approval->service_type) {
                                                        case 'hotel':
                                                            $service = \App\Models\SupplierHotel::find($approval->service_id);
                                                            $serviceName = $service->name ?? 'N/A';
                                                            break;
                                                        case 'tour':
                                                            $service = \App\Models\SupplierTour::find($approval->service_id);
                                                            $serviceName = $service->title ?? 'N/A';
                                                            break;
                                                        case 'room':
                                                            $service = \App\Models\SupplierRoom::find($approval->service_id);
                                                            $serviceName = $service->name ?? 'N/A';
                                                            break;
                                                        case 'trip':
                                                            $service = \App\Models\SupplierTrip::find($approval->service_id);
                                                            $serviceName = $service->trip_name ?? 'N/A';
                                                            break;
                                                        case 'transport':
                                                            $service = \App\Models\SupplierTransport::find($approval->service_id);
                                                            $serviceName = $service->name ?? 'N/A';
                                                            break;
                                                    }
                                                @endphp
                                                {{ $serviceName }}
                                            </td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($approval->service_type) }}</span></td>
                                            <td>{{ $approval->supplier->user->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($approval->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($approval->status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="digits">{{ $approval->created_at->format('M Y, d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">No Service Approvals Found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>

                                <a href="{{ route('dashboard.service-approvals.index') }}" class="btn btn-primary mt-4">View All Approvals</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
