@extends('layouts.dashboard.app')

@section('title', 'Room Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Room Details: {{ $room->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('supplier.rooms.edit', $room) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Room
                        </a>
                        <a href="{{ route('supplier.rooms.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Rooms
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Name:</strong>
                                            <p>{{ $room->name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Description:</strong>
                                            <p>{{ $room->description ?: 'No description available' }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Hotel:</strong>
                                            <p>{{ $room->supplierHotel->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Slug:</strong>
                                            <p>{{ $room->slug }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Status:</strong>
                                            <span class="badge badge-{{ $room->status_color }}">
                                                {{ $room->status_label }}
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Created:</strong>
                                            <p>{{ $room->created_at->format('M Y, d H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Specifications -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Room Specifications</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Bed Count:</strong>
                                            <p>{{ $room->bed_count }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Room Type:</strong>
                                            <p>{{ $room->room_type ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Max Capacity:</strong>
                                            <p>{{ $room->max_capacity ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Night Price:</strong>
                                            <p>${{ number_format($room->night_price, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Bed Types:</strong>
                                            <p>{{ $room->bed_types ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Extra Bed Available:</strong>
                                            <p>{{ $room->extra_bed_available ? 'Yes' : 'No' }}</p>
                                        </div>
                                    </div>
                                    @if($room->extra_bed_available)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Extra Bed Price:</strong>
                                            <p>${{ number_format($room->extra_bed_price, 2) }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Max Extra Beds:</strong>
                                            <p>{{ $room->max_extra_beds }}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Extra Bed Description:</strong>
                                            <p>{{ $room->extra_bed_description ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Amenities -->
                            @if($room->amenities->count() > 0)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Amenities</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($room->amenities as $amenity)
                                            <div class="col-md-4 mb-2">
                                                <span class="badge badge-info">{{ $amenity->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Media -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Media</h5>
                                </div>
                                <div class="card-body">
                                    @if($room->featured_image)
                                    <div class="mb-3">
                                        <strong>Featured Image:</strong>
                                        <img src="{{ asset('storage/' . $room->featured_image) }}" 
                                             alt="Featured Image" class="img-fluid rounded">
                                    </div>
                                    @endif

                                    @if($room->banner)
                                    <div class="mb-3">
                                        <strong>Banner:</strong>
                                        <img src="{{ asset('storage/' . $room->banner) }}" 
                                             alt="Banner" class="img-fluid rounded">
                                    </div>
                                    @endif

                                    @if($room->gallery && count($room->gallery) > 0)
                                    <div class="mb-3">
                                        <strong>Gallery:</strong>
                                        <div class="row">
                                            @foreach($room->gallery as $image)
                                                <div class="col-6 mb-2">
                                                    <img src="{{ asset('storage/' . $image) }}" 
                                                         alt="Gallery Image" class="img-fluid rounded">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- SEO Information -->
                            @if($room->seo)
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="mb-0">SEO Information</h5>
                                </div>
                                <div class="card-body">
                                    @foreach(config('translatable.supported_locales') as $locale => $localeData)
                                        @if($room->seo->translate($locale))
                                        <div class="mb-3">
                                            <h6>{{ $localeData['native'] }}</h6>
                                            @if($room->seo->translate($locale)->meta_title)
                                            <div class="mb-1">
                                                <small class="text-muted">Meta Title:</small>
                                                <p class="mb-0">{{ $room->seo->translate($locale)->meta_title }}</p>
                                            </div>
                                            @endif
                                            @if($room->seo->translate($locale)->meta_description)
                                            <div class="mb-1">
                                                <small class="text-muted">Meta Description:</small>
                                                <p class="mb-0">{{ $room->seo->translate($locale)->meta_description }}</p>
                                            </div>
                                            @endif
                                            @if($room->seo->translate($locale)->meta_keywords)
                                            <div class="mb-1">
                                                <small class="text-muted">Meta Keywords:</small>
                                                <p class="mb-0">{{ $room->seo->translate($locale)->meta_keywords }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endif
                                    @endforeach
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
@endsection
