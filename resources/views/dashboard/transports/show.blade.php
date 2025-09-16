@extends('layouts.dashboard.app')

@section('content')
    <!-- Container-fluid starts-->
    <x-dashboard.partials.breadcrumb title="Transport Details" :hideFirst="true">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard.transports.index') }}">Transports</a>
        </li>
    </x-dashboard.partials.breadcrumb>
    <!-- Container-fluid Ends-->

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <x-dashboard.partials.message-alert/>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Transport Details</h5>
                            <div>
                                <a href="{{ route('dashboard.transports.edit', $transport) }}" class="btn btn-warning">
                                    <i class="mdi mdi-pencil"></i> Edit Transport
                                </a>
                                <a href="{{ route('dashboard.transports.index') }}" class="btn btn-secondary">
                                    <i class="mdi mdi-arrow-left"></i> Back to Transports
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Transport Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="150"><strong>Name:</strong></td>
                                                <td>{{ $transport->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Origin:</strong></td>
                                                <td><span class="badge bg-primary">{{ $transport->origin_location }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Destination:</strong></td>
                                                <td><span class="badge bg-success">{{ $transport->destination_location }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Route Type:</strong></td>
                                                <td><span class="badge bg-info">{{ ucfirst($transport->route_type) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Transport Type:</strong></td>
                                                <td><span class="badge bg-warning">{{ ucfirst($transport->transport_type) }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Vehicle Type:</strong></td>
                                                <td>
                                                    @if($transport->vehicle_type)
                                                        <span class="badge bg-warning">{{ ucfirst($transport->vehicle_type) }}</span>
                                                        @if($transport->seating_capacity)
                                                            <br><small class="text-muted">{{ $transport->seating_capacity }} seats</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    @if($transport->enabled)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Price:</strong></td>
                                                <td><span class="badge bg-success">{{ $transport->formatted_price }}</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Travel Time:</strong></td>
                                                <td><span class="badge bg-secondary">{{ $transport->formatted_travel_time }}</span></td>
                                            </tr>
                                            @if($transport->distance)
                                            <tr>
                                                <td><strong>Distance:</strong></td>
                                                <td><span class="badge bg-info">{{ $transport->formatted_distance }}</span></td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Created:</strong></td>
                                                <td>{{ $transport->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Updated:</strong></td>
                                                <td>{{ $transport->updated_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        </table>

                                        @if($transport->intermediate_stops)
                                        <div class="mt-3">
                                            <h6>Intermediate Stops:</h6>
                                            <p class="text-muted">{{ is_array($transport->intermediate_stops) ? implode(', ', $transport->intermediate_stops) : $transport->intermediate_stops }}</p>
                                        </div>
                                        @endif

                                        @if($transport->amenities && count($transport->amenities) > 0)
                                        <div class="mt-3">
                                            <h6>Amenities:</h6>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($transport->amenities as $amenity)
                                                    <span class="badge bg-light text-dark">{{ $amenity->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                @if($transport->description)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Description</h6>
                                    </div>
                                    <div class="card-body">
                                        {!! $transport->description !!}
                                    </div>
                                </div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Actions</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('dashboard.transports.edit', $transport) }}" class="btn btn-warning">
                                                <i class="mdi mdi-pencil"></i> Edit Transport
                                            </a>
                                            <form action="{{ route('dashboard.transports.destroy', $transport) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this transport?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="mdi mdi-delete"></i> Delete Transport
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Transport Statistics</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="h4 text-primary">{{ $transport->bookings_count ?? 0 }}</div>
                                                <small class="text-muted">Total Bookings</small>
                                            </div>
                                            <div class="col-6">
                                                <div class="h4 text-success">{{ $transport->total_revenue ?? 0 }}</div>
                                                <small class="text-muted">Total Revenue</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($transport->featured_image)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Featured Image</h6>
                                    </div>
                                    <div class="card-body">
                                        <img src="{{ asset('storage/' . $transport->featured_image) }}" 
                                             alt="Featured Image" 
                                             class="img-fluid rounded">
                                    </div>
                                </div>
                                @endif

                                @if($transport->images && count($transport->images) > 0)
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h6 class="mb-0">Gallery Images</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($transport->images as $image)
                                                <div class="col-6 mb-2">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                         alt="Gallery Image" 
                                                         class="img-fluid rounded">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection
