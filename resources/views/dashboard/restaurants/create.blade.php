@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Restaurant">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.restaurants.index') }}">Restaurants</a></li>
            <li class="breadcrumb-item active">Create</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Create Restaurant</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.restaurants.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            @error('city_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="2" placeholder="Restaurant address">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 123-4567">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" placeholder="restaurant@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                                   id="website" name="website" value="{{ old('website') }}" placeholder="https://example.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cuisine_type" class="form-label">Primary Cuisine Type</label>
                                            <input type="text" class="form-control @error('cuisine_type') is-invalid @enderror" 
                                                   id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type') }}" placeholder="e.g., Italian, Mediterranean, Asian">
                                            @error('cuisine_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price_range" class="form-label">Price Range</label>
                                            <select class="form-control @error('price_range') is-invalid @enderror" 
                                                    id="price_range" name="price_range">
                                                <option value="">Select Price Range</option>
                                                <option value="budget" {{ old('price_range') == 'budget' ? 'selected' : '' }}>$ - Budget</option>
                                                <option value="moderate" {{ old('price_range') == 'moderate' ? 'selected' : '' }}>$$ - Moderate</option>
                                                <option value="expensive" {{ old('price_range') == 'expensive' ? 'selected' : '' }}>$$$ - Expensive</option>
                                                <option value="luxury" {{ old('price_range') == 'luxury' ? 'selected' : '' }}>$$$$ - Luxury</option>
                                            </select>
                                            @error('price_range')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_meal" class="form-label">Price Per Meal</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_meal') is-invalid @enderror" 
                                                   id="price_per_meal" name="price_per_meal" value="{{ old('price_per_meal') }}" min="0" placeholder="0.00">
                                            @error('price_per_meal')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="form-control @error('currency') is-invalid @enderror" 
                                                    id="currency" name="currency" required>
                                                <option value="">Select Currency</option>
                                                @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                                    <option value="{{ $currencyCode }}" {{ old('currency', 'USD') == $currencyCode ? 'selected' : '' }}>
                                                        {{ $currencyCode }} - {{ $currencyName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity</label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity') }}" min="0" placeholder="Maximum number of guests">
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="opening_hours" class="form-label">Opening Hours</label>
                                            <textarea class="form-control @error('opening_hours') is-invalid @enderror" 
                                                      id="opening_hours" name="opening_hours" rows="2" placeholder="e.g., Mon-Sun: 11:00 AM - 11:00 PM">{{ old('opening_hours') }}</textarea>
                                            @error('opening_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="dress_code" class="form-label">Dress Code</label>
                                            <select class="form-control @error('dress_code') is-invalid @enderror" 
                                                    id="dress_code" name="dress_code">
                                                <option value="">Select Dress Code</option>
                                                <option value="casual" {{ old('dress_code') == 'casual' ? 'selected' : '' }}>Casual</option>
                                                <option value="smart_casual" {{ old('dress_code') == 'smart_casual' ? 'selected' : '' }}>Smart Casual</option>
                                                <option value="business" {{ old('dress_code') == 'business' ? 'selected' : '' }}>Business</option>
                                                <option value="formal" {{ old('dress_code') == 'formal' ? 'selected' : '' }}>Formal</option>
                                            </select>
                                            @error('dress_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="features" class="form-label">Features</label>
                                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                                      id="features" name="features" rows="2" placeholder="Special features (e.g., Outdoor seating, Live music, Wine bar)">{{ old('features') }}</textarea>
                                            @error('features')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="amenities" class="form-label">Amenities</label>
                                            <textarea class="form-control @error('amenities') is-invalid @enderror" 
                                                      id="amenities" name="amenities" rows="2" placeholder="Available amenities (e.g., WiFi, Parking, Wheelchair Accessible)">{{ old('amenities') }}</textarea>
                                            @error('amenities')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="2" placeholder="Additional notes">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                                   {{ old('active') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="reservation_required" name="reservation_required" 
                                                   {{ old('reservation_required') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reservation_required">
                                                Reservation Required
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Create Restaurant
                                        </button>
                                        <a href="{{ route('dashboard.restaurants.index') }}" class="btn btn-secondary ms-2">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
