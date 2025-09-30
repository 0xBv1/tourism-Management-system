@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Guide Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.guides.index') }}">Guides</a></li>
            <li class="breadcrumb-item active">{{ $guide->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Guide Details Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Guide Information</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                            <div class="card-header-right">
                                @if(admin()->can('guides.edit'))
                                    <a href="{{ route('dashboard.guides.edit', $guide) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('dashboard.guides.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Guide Name:</strong></td>
                                            <td>{{ $guide->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $guide->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $guide->phone }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nationality:</strong></td>
                                            <td>{{ $guide->nationality ?? 'Not specified' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $guide->city->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Experience:</strong></td>
                                            <td>{{ $guide->experience_years }} years</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $guide->status->getColor() }}">
                                                    {{ $guide->status->getLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Price Per Hour:</strong></td>
                                            <td>{{ $guide->price_per_hour ? '$' . number_format($guide->price_per_hour, 2) . ' ' . $guide->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Price Per Day:</strong></td>
                                            <td>{{ $guide->price_per_day ? '$' . number_format($guide->price_per_day, 2) . ' ' . $guide->currency : 'Not set' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emergency Contact:</strong></td>
                                            <td>{{ $guide->emergency_contact ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Emergency Phone:</strong></td>
                                            <td>{{ $guide->emergency_phone ?? 'Not provided' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Active:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $guide->active ? 'success' : 'danger' }}">
                                                    {{ $guide->active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Enabled:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $guide->enabled ? 'success' : 'danger' }}">
                                                    {{ $guide->enabled ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($guide->bio)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Bio:</strong></h6>
                                    <p>{{ $guide->bio }}</p>
                                </div>
                            </div>
                            @endif

                            @if($guide->notes)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6><strong>Notes:</strong></h6>
                                    <p>{{ $guide->notes }}</p>
                                </div>
                            </div>
                            @endif

                            <!-- Languages -->
                            @php
                                $languages = is_string($guide->languages) ? json_decode($guide->languages, true) : $guide->languages;
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
                                $specializations = is_string($guide->specializations) ? json_decode($guide->specializations, true) : $guide->specializations;
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

                            <!-- Certifications -->
                            @php
                                $certifications = is_string($guide->certifications) ? json_decode($guide->certifications, true) : $guide->certifications;
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
                                $availability = is_string($guide->availability_schedule) ? json_decode($guide->availability_schedule, true) : $guide->availability_schedule;
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

                    <!-- Guide Bookings -->
                    @if($guide->bookings && $guide->bookings->count() > 0)
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
                                        @foreach($guide->bookings->take(10) as $booking)
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
