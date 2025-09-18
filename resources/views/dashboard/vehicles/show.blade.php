@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Vehicle Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.vehicles.index') }}">Vehicles</a></li>
            <li class="breadcrumb-item active">{{ $vehicle->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Vehicle Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Vehicle Information</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.vehicles.edit', $vehicle) }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('dashboard.vehicles.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Vehicle Name:</strong></td>
                                            <td>{{ $vehicle->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td><span class="badge bg-primary">{{ ucfirst($vehicle->type) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Brand:</strong></td>
                                            <td>{{ $vehicle->brand }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Model:</strong></td>
                                            <td>{{ $vehicle->model }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Year:</strong></td>
                                            <td>{{ $vehicle->year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>License Plate:</strong></td>
                                            <td><span class="badge bg-info">{{ $vehicle->license_plate }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Capacity:</strong></td>
                                            <td>{{ $vehicle->capacity }} passengers</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $vehicle->city->name }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Driver Name:</strong></td>
                                            <td>{{ $vehicle->driver_name ?? 'Not assigned' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Driver Phone:</strong></td>
                                            <td>{{ $vehicle->driver_phone ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Driver License:</strong></td>
                                            <td>{{ $vehicle->driver_license ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price Per Hour:</strong></td>
                                            <td>{{ $vehicle->price_per_hour ? '$' . number_format($vehicle->price_per_hour, 2) . ' ' . $vehicle->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price Per Day:</strong></td>
                                            <td>{{ $vehicle->price_per_day ? '$' . number_format($vehicle->price_per_day, 2) . ' ' . $vehicle->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fuel Type:</strong></td>
                                            <td>{{ $vehicle->fuel_type ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Transmission:</strong></td>
                                            <td>{{ $vehicle->transmission ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $vehicle->status->getColor() }}">
                                                    {{ $vehicle->status->getLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($vehicle->description)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Description:</strong></h6>
                                    <p>{{ $vehicle->description }}</p>
                                </div>
                            </div>
                            @endif

                            @if($vehicle->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Notes:</strong></h6>
                                    <p>{{ $vehicle->notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Maintenance Information -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Maintenance Information:</strong></h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Insurance Expiry</h6>
                                                    <p class="card-text">
                                                        {{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Registration Expiry</h6>
                                                    <p class="card-text">
                                                        {{ $vehicle->registration_expiry ? $vehicle->registration_expiry->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Last Maintenance</h6>
                                                    <p class="card-text">
                                                        {{ $vehicle->last_maintenance ? $vehicle->last_maintenance->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h6 class="card-title">Next Maintenance</h6>
                                                    <p class="card-text">
                                                        {{ $vehicle->next_maintenance ? $vehicle->next_maintenance->format('M d, Y') : 'Not set' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Information -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Status Information:</strong></h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" {{ $vehicle->active ? 'checked' : 'disabled' }}>
                                                <label class="form-check-label">
                                                    Active
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" {{ $vehicle->enabled ? 'checked' : 'disabled' }}>
                                                <label class="form-check-label">
                                                    Enabled
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" {{ $vehicle->deleted_at ? 'checked' : 'disabled' }}>
                                                <label class="form-check-label">
                                                    Deleted
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Bookings -->
                    @if($vehicle->bookings && $vehicle->bookings->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5>Recent Bookings</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Booking ID</th>
                                            <th>Client</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Total Price</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($vehicle->bookings->take(10) as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->bookingFile?->inquiry?->client?->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->start_date->format('M d, Y') }}</td>
                                            <td>{{ $booking->end_date->format('M d, Y') }}</td>
                                            <td>${{ number_format($booking->total_price, 2) }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $booking->status }}</span>
                                            </td>
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
        <!-- Container-fluid Ends-->
    </div>
@endsection
