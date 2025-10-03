@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Dahabia">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.dahabias.index') }}">Dahabias</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Edit Dahabia</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.dahabias.update', $dahabia) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Dahabia Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $dahabia->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city_id" class="form-label">City <span class="text-danger">*</span></label>
                                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                                    id="city_id" name="city_id" required>
                                                <option value="">Select City</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id', $dahabia->city_id) == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                                                      id="description" name="description" rows="3">{{ old('description', $dahabia->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Maximum Capacity</label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity', $dahabia->capacity) }}" min="1" placeholder="Number of passengers">
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_person" class="form-label">Price Per Person</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_person') is-invalid @enderror" 
                                                   id="price_per_person" name="price_per_person" value="{{ old('price_per_person', $dahabia->price_per_person) }}" min="0" placeholder="0.00">
                                            @error('price_per_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_charter" class="form-label">Price Per Charter</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_charter') is-invalid @enderror" 
                                                   id="price_per_charter" name="price_per_charter" value="{{ old('price_per_charter', $dahabia->price_per_charter) }}" min="0" placeholder="0.00">
                                            @error('price_per_charter')
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
                                                    <option value="{{ $currencyCode }}" {{ old('currency', $dahabia->currency ?? 'USD') == $currencyCode ? 'selected' : '' }}>
                                                        {{ $currencyCode }} - {{ $currencyName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departure_location" class="form-label">Departure Location</label>
                                            <input type="text" class="form-control @error('departure_location') is-invalid @enderror" 
                                                   id="departure_location" name="departure_location" value="{{ old('departure_location', $dahabia->departure_location) }}" placeholder="Starting point">
                                            @error('departure_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="arrival_location" class="form-label">Arrival Location</label>
                                            <input type="text" class="form-control @error('arrival_location') is-invalid @enderror" 
                                                   id="arrival_location" name="arrival_location" value="{{ old('arrival_location', $dahabia->arrival_location) }}" placeholder="Ending point">
                                            @error('arrival_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="route_description" class="form-label">Route Description</label>
                                            <textarea class="form-control @error('route_description') is-invalid @enderror" 
                                                      id="route_description" name="route_description" rows="3" placeholder="Describe the sailing route">{{ old('route_description', $dahabia->route_description) }}</textarea>
                                            @error('route_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="crew_count" class="form-label">Crew Count</label>
                                            <input type="number" class="form-control @error('crew_count') is-invalid @enderror" 
                                                   id="crew_count" name="crew_count" value="{{ old('crew_count', $dahabia->crew_count) }}" min="0" placeholder="Number of crew members">
                                            @error('crew_count')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="duration_nights" class="form-label">Duration (Nights)</label>
                                            <input type="number" class="form-control @error('duration_nights') is-invalid @enderror" 
                                                   id="duration_nights" name="duration_nights" value="{{ old('duration_nights', $dahabia->duration_nights) }}" min="0" placeholder="Number of nights">
                                            @error('duration_nights')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="meal_plan" class="form-label">Meal Plan</label>
                                            <select class="form-control @error('meal_plan') is-invalid @enderror" 
                                                    id="meal_plan" name="meal_plan">
                                                <option value="">Select Meal Plan</option>
                                                <option value="full_board" {{ old('meal_plan', $dahabia->meal_plan) == 'full_board' ? 'selected' : '' }}>Full Board</option>
                                                <option value="half_board" {{ old('meal_plan', $dahabia->meal_plan) == 'half_board' ? 'selected' : '' }}>Half Board</option>
                                                <option value="bed_breakfast" {{ old('meal_plan', $dahabia->meal_plan) == 'bed_breakfast' ? 'selected' : '' }}>Bed & Breakfast</option>
                                                <option value="all_inclusive" {{ old('meal_plan', $dahabia->meal_plan) == 'all_inclusive' ? 'selected' : '' }}>All Inclusive</option>
                                            </select>
                                            @error('meal_plan')
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
                                                      id="amenities" name="amenities" rows="2" placeholder="List available amenities (e.g., Air conditioning, WiFi, Minibar)">{{ old('amenities', $dahabia->amenities) }}</textarea>
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
                                                      id="notes" name="notes" rows="2" placeholder="Additional notes">{{ old('notes', $dahabia->notes) }}</textarea>
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
                                                   {{ old('active', $dahabia->active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled', $dahabia->enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Dahabia
                                        </button>
                                        <a href="{{ route('dashboard.dahabias.index') }}" class="btn btn-secondary ms-2">
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
