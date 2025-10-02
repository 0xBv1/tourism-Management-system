@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Nile Cruise">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.nile-cruises.index') }}">Nile Cruises</a></li>
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
                            <h5>Create Nile Cruise</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.nile-cruises.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nile Cruise Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
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
                                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
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
                                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="vessel_type" class="form-label">Vessel Type <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('vessel_type') is-invalid @enderror" 
                                                   id="vessel_type" name="vessel_type" value="{{ old('vessel_type') }}" 
                                                   placeholder="e.g., Luxury Ship, Traditional Felucca" required>
                                            @error('vessel_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
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
                                                   id="price_per_person" name="price_per_person" value="{{ old('price_per_person') }}" min="0" placeholder="0.00">
                                            @error('price_per_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">

                                            <input type="number" step="0.01" class="form-control @error('price_per_cabin') is-invalid @enderror" 
                                                   id="price_per_cabin" name="price_per_cabin" value="{{ old('price_per_cabin') }}" min="0" placeholder="0.00">
                                            @error('price_per_cabin')
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
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departure_location" class="form-label">Departure Location <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('departure_location') is-invalid @enderror" 
                                                   id="departure_location" name="departure_location" value="{{ old('departure_location') }}" required>
                                            @error('departure_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="arrival_location" class="form-label">Arrival Location <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('arrival_location') is-invalid @enderror" 
                                                   id="arrival_location" name="arrival_location" value="{{ old('arrival_location') }}" required>
                                            @error('arrival_location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="duration_nights" class="form-label">Duration (Nights) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('duration_nights') is-invalid @enderror" 
                                                   id="duration_nights" name="duration_nights" value="{{ old('duration_nights') }}" min="1" required>
                                            @error('duration_nights')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="check_in_time" class="form-label">Check-in Time</label>
                                            <input type="time" class="form-control @error('check_in_time') is-invalid @enderror" 
                                                   id="check_in_time" name="check_in_time" value="{{ old('check_in_time') }}">
                                            @error('check_in_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="check_out_time" class="form-label">Check-out Time</label>
                                            <input type="time" class="form-control @error('check_out_time') is-invalid @enderror" 
                                                   id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}">
                                            @error('check_out_time')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="meal_plan" class="form-label">Meal Plan</label>
                                            <select class="form-control @error('meal_plan') is-invalid @enderror" 
                                                    id="meal_plan" name="meal_plan">
                                                <option value="">Select Meal Plan</option>
                                                <option value="Bed & Breakfast" {{ old('meal_plan') == 'Bed & Breakfast' ? 'selected' : '' }}>Bed & Breakfast</option>
                                                <option value="Half Board" {{ old('meal_plan') == 'Half Board' ? 'selected' : '' }}>Half Board</option>
                                                <option value="Full Board" {{ old('meal_plan') == 'Full Board' ? 'selected' : '' }}>Full Board</option>
                                                <option value="All Inclusive" {{ old('meal_plan') == 'All Inclusive' ? 'selected' : '' }}>All Inclusive</option>
                                            </select>
                                            @error('meal_plan')
                                                @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                <option value="out_of_service" {{ old('status') == 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                                            </select>
                                            @error('status')
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
                                                      id="amenities" name="amenities" rows="2" placeholder="List available amenities (e.g., Air conditioning, WiFi, Swimming pool)">{{ old('amenities') }}</textarea>
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

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Create Nile Cruise
                                        </button>
                                        <a href="{{ route('dashboard.nile-cruises.index') }}" class="btn btn-secondary ms-2">
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
