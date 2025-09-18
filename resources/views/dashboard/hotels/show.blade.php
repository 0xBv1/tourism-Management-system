@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Hotel Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.hotels.index') }}">Hotels</a></li>
            <li class="breadcrumb-item active">{{ $hotel->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ $hotel->name }}</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.hotels.edit', $hotel) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Basic Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $hotel->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $hotel->city->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Star Rating:</strong></td>
                                            <td>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star {{ $i <= $hotel->star_rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                ({{ $hotel->star_rating }} stars)
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $hotel->status_color }}">
                                                    {{ $hotel->status_label }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address:</strong></td>
                                            <td>{{ $hotel->address }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Room Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Total Rooms:</strong></td>
                                            <td>{{ $hotel->total_rooms }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Available Rooms:</strong></td>
                                            <td>{{ $hotel->available_rooms }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Utilization:</strong></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $hotel->utilization_percentage }}%"
                                                         aria-valuenow="{{ $hotel->utilization_percentage }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ $hotel->utilization_percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price Per Night:</strong></td>
                                            <td>{{ $hotel->currency }} {{ number_format($hotel->price_per_night, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Active:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $hotel->active ? 'success' : 'danger' }}">
                                                    {{ $hotel->active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Enabled:</strong></td>
                                            <td>
                                                <span class="badge badge-{{ $hotel->enabled ? 'success' : 'danger' }}">
                                                    {{ $hotel->enabled ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($hotel->description)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Description</h6>
                                        <p>{{ $hotel->description }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($hotel->amenities && count($hotel->amenities) > 0)
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Amenities</h6>
                                        <div class="d-flex flex-wrap">
                                            @foreach($hotel->amenities as $amenity)
                                                <span class="badge badge-primary me-2 mb-2">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($hotel->bookings && count($hotel->bookings) > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6>Recent Bookings</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Booking File</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                        <th>Status</th>
                                                        <th>Total Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($hotel->bookings->take(5) as $booking)
                                                        <tr>
                                                            <td>
                                                                <a href="{{ route('dashboard.bookings.show', $booking->bookingFile) }}">
                                                                    {{ $booking->bookingFile->file_name }}
                                                                </a>
                                                            </td>
                                                            <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                                            <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $booking->status_color }}">
                                                                    {{ $booking->status_label }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $booking->currency }} {{ number_format($booking->total_price, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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




