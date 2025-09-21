@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Representative Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.representatives.index') }}">Representatives</a></li>
            <li class="breadcrumb-item active">{{ $representative->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Representative Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Representative Information</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                            <div class="card-header-right">
                                @if(admin()->can('representatives.edit'))
                                    <a href="{{ route('dashboard.representatives.edit', $representative) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('dashboard.representatives.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Representative Name:</strong></td>
                                            <td>{{ $representative->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $representative->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $representative->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nationality:</strong></td>
                                            <td>{{ $representative->nationality ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $representative->city->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Experience:</strong></td>
                                            <td>{{ $representative->experience_years }} years</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $representative->status->getColor() }}">
                                                    {{ $representative->status->getLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Price Per Hour:</strong></td>
                                            <td>{{ $representative->price_per_hour ? '$' . number_format($representative->price_per_hour, 2) . ' ' . $representative->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price Per Day:</strong></td>
                                            <td>{{ $representative->price_per_day ? '$' . number_format($representative->price_per_day, 2) . ' ' . $representative->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emergency Contact:</strong></td>
                                            <td>{{ $representative->emergency_contact ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emergency Phone:</strong></td>
                                            <td>{{ $representative->emergency_phone ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Active:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $representative->active ? 'success' : 'danger' }}">
                                                    {{ $representative->active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Enabled:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $representative->enabled ? 'success' : 'danger' }}">
                                                    {{ $representative->enabled ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Company Information -->
                            @if($representative->company_name || $representative->company_license)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Company Information:</strong></h6>
                                    <div class="row">
                                        @if($representative->company_name)
                                        <div class="col-md-6">
                                            <strong>Company Name:</strong> {{ $representative->company_name }}
                                        </div>
                                        @endif
                                        @if($representative->company_license)
                                        <div class="col-md-6">
                                            <strong>Company License:</strong> {{ $representative->company_license }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($representative->bio)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Bio:</strong></h6>
                                    <p>{{ $representative->bio }}</p>
                                </div>
                            </div>
                            @endif

                            @if($representative->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Notes:</strong></h6>
                                    <p>{{ $representative->notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Languages -->
                            @php
                                $languages = is_string($representative->languages) ? json_decode($representative->languages, true) : $representative->languages;
                            @endphp
                            @if($languages && count($languages) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Languages:</strong></h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($languages as $language)
                                            <span class="badge bg-primary">{{ $language }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Specializations -->
                            @php
                                $specializations = is_string($representative->specializations) ? json_decode($representative->specializations, true) : $representative->specializations;
                            @endphp
                            @if($specializations && count($specializations) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Specializations:</strong></h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($specializations as $specialization)
                                            <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $specialization)) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Service Areas -->
                            @php
                                $serviceAreas = is_string($representative->service_areas) ? json_decode($representative->service_areas, true) : $representative->service_areas;
                            @endphp
                            @if($serviceAreas && count($serviceAreas) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Service Areas:</strong></h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($serviceAreas as $area)
                                            <span class="badge bg-warning">{{ ucwords(str_replace('_', ' ', $area)) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Certifications -->
                            @php
                                $certifications = is_string($representative->certifications) ? json_decode($representative->certifications, true) : $representative->certifications;
                            @endphp
                            @if($certifications && count($certifications) > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Certifications:</strong></h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($certifications as $certification)
                                            <span class="badge bg-success">{{ $certification }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Availability Schedule -->
                            @php
                                $availability = is_string($representative->availability_schedule) ? json_decode($representative->availability_schedule, true) : $representative->availability_schedule;
                            @endphp
                            @if($availability)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6><strong>Availability Schedule:</strong></h6>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['monday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Monday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['tuesday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Tuesday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['wednesday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Wednesday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['thursday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Thursday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['friday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Friday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['saturday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Saturday</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-2">
                                            <div class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" {{ $availability['sunday'] ?? false ? 'checked' : 'disabled' }}>
                                                    <label class="form-check-label">Sunday</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Representative Bookings -->
                    @if($representative->bookings && $representative->bookings->count() > 0)
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
                                        @foreach($representative->bookings->take(10) as $booking)
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
