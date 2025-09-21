@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Vehicle">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.vehicles.index') }}">Vehicles</a></li>
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
                            <h5>Edit Vehicle: {{ $vehicle->name }}</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Vehicle Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $vehicle->name) }}" required>
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
                                                    <option value="{{ $city->id }}" {{ old('city_id', $vehicle->city_id) == $city->id ? 'selected' : '' }}>
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
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                                            <select class="form-control @error('type') is-invalid @enderror" 
                                                    id="type" name="type" required>
                                                <option value="">Select Type</option>
                                                <option value="sedan" {{ old('type', $vehicle->type) == 'sedan' ? 'selected' : '' }}>Sedan</option>
                                                <option value="suv" {{ old('type', $vehicle->type) == 'suv' ? 'selected' : '' }}>SUV</option>
                                                <option value="bus" {{ old('type', $vehicle->type) == 'bus' ? 'selected' : '' }}>Bus</option>
                                                <option value="van" {{ old('type', $vehicle->type) == 'van' ? 'selected' : '' }}>Van</option>
                                                <option value="truck" {{ old('type', $vehicle->type) == 'truck' ? 'selected' : '' }}>Truck</option>
                                            </select>
                                            @error('type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="brand" class="form-label">Brand <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('brand') is-invalid @enderror" 
                                                   id="brand" name="brand" value="{{ old('brand', $vehicle->brand) }}" required>
                                            @error('brand')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="model" class="form-label">Model <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                                   id="model" name="model" value="{{ old('model', $vehicle->model) }}" required>
                                            @error('model')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                                   id="year" name="year" value="{{ old('year', $vehicle->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
                                            @error('year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="license_plate" class="form-label">License Plate <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('license_plate') is-invalid @enderror" 
                                                   id="license_plate" name="license_plate" value="{{ old('license_plate', $vehicle->license_plate) }}" required>
                                            @error('license_plate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity', $vehicle->capacity) }}" min="1" required>
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="driver_name" class="form-label">Driver Name</label>
                                            <input type="text" class="form-control @error('driver_name') is-invalid @enderror" 
                                                   id="driver_name" name="driver_name" value="{{ old('driver_name', $vehicle->driver_name) }}">
                                            @error('driver_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="driver_phone" class="form-label">Driver Phone</label>
                                            <input type="text" class="form-control @error('driver_phone') is-invalid @enderror" 
                                                   id="driver_phone" name="driver_phone" value="{{ old('driver_phone', $vehicle->driver_phone) }}">
                                            @error('driver_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_hour" class="form-label">Price Per Hour</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_hour') is-invalid @enderror" 
                                                   id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour', $vehicle->price_per_hour) }}" min="0">
                                            @error('price_per_hour')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_day" class="form-label">Price Per Day</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_day') is-invalid @enderror" 
                                                   id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $vehicle->price_per_day) }}" min="0">
                                            @error('price_per_day')
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
                                                <option value="USD" {{ old('currency', $vehicle->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ old('currency', $vehicle->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ old('currency', $vehicle->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
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
                                            <label for="fuel_type" class="form-label">Fuel Type</label>
                                            <select class="form-control @error('fuel_type') is-invalid @enderror" 
                                                    id="fuel_type" name="fuel_type">
                                                <option value="">Select Fuel Type</option>
                                                <option value="Gasoline" {{ old('fuel_type', $vehicle->fuel_type) == 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                                <option value="Diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                                <option value="Electric" {{ old('fuel_type', $vehicle->fuel_type) == 'Electric' ? 'selected' : '' }}>Electric</option>
                                                <option value="Hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                            </select>
                                            @error('fuel_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="transmission" class="form-label">Transmission</label>
                                            <select class="form-control @error('transmission') is-invalid @enderror" 
                                                    id="transmission" name="transmission">
                                                <option value="">Select Transmission</option>
                                                <option value="Manual" {{ old('transmission', $vehicle->transmission) == 'Manual' ? 'selected' : '' }}>Manual</option>
                                                <option value="Automatic" {{ old('transmission', $vehicle->transmission) == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                                <option value="Semi-Automatic" {{ old('transmission', $vehicle->transmission) == 'Semi-Automatic' ? 'selected' : '' }}>Semi-Automatic</option>
                                            </select>
                                            @error('transmission')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="insurance_expiry" class="form-label">Insurance Expiry</label>
                                            <input type="date" class="form-control @error('insurance_expiry') is-invalid @enderror" 
                                                   id="insurance_expiry" name="insurance_expiry" value="{{ old('insurance_expiry', $vehicle->insurance_expiry?->format('Y-m-d')) }}">
                                            @error('insurance_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="registration_expiry" class="form-label">Registration Expiry</label>
                                            <input type="date" class="form-control @error('registration_expiry') is-invalid @enderror" 
                                                   id="registration_expiry" name="registration_expiry" value="{{ old('registration_expiry', $vehicle->registration_expiry?->format('Y-m-d')) }}">
                                            @error('registration_expiry')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="last_maintenance" class="form-label">Last Maintenance</label>
                                            <input type="date" class="form-control @error('last_maintenance') is-invalid @enderror" 
                                                   id="last_maintenance" name="last_maintenance" value="{{ old('last_maintenance', $vehicle->last_maintenance?->format('Y-m-d')) }}">
                                            @error('last_maintenance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="next_maintenance" class="form-label">Next Maintenance</label>
                                            <input type="date" class="form-control @error('next_maintenance') is-invalid @enderror" 
                                                   id="next_maintenance" name="next_maintenance" value="{{ old('next_maintenance', $vehicle->next_maintenance?->format('Y-m-d')) }}">
                                            @error('next_maintenance')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description', $vehicle->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status', $vehicle->status->value) == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                                   {{ old('active', $vehicle->active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled', $vehicle->enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('dashboard.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
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
