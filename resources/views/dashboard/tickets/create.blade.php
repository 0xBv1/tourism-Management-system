@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Ticket">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.tickets.index') }}">Tickets</a></li>
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
                            <h5>Create Ticket</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.tickets.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Ticket Name <span class="text-danger">*</span></label>
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price_per_person" class="form-label">Price Per Person <span class="text-danger">*</span></label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_person') is-invalid @enderror" 
                                                   id="price_per_person" name="price_per_person" value="{{ old('price_per_person') }}" min="0" required>
                                            @error('price_per_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="form-control @error('currency') is-invalid @enderror" 
                                                    id="currency" name="currency" required>
                                                <option value="">Select Currency</option>
                                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                                <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>EGP</option>
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
                                            <label for="duration_hours" class="form-label">Duration (Hours)</label>
                                            <input type="number" step="0.5" class="form-control @error('duration_hours') is-invalid @enderror" 
                                                   id="duration_hours" name="duration_hours" value="{{ old('duration_hours') }}" min="0">
                                            @error('duration_hours')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_participants" class="form-label">Maximum Participants</label>
                                            <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                                   id="max_participants" name="max_participants" value="{{ old('max_participants') }}" min="1">
                                            @error('max_participants')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_age" class="form-label">Minimum Age</label>
                                            <input type="number" class="form-control @error('min_age') is-invalid @enderror" 
                                                   id="min_age" name="min_age" value="{{ old('min_age') }}" min="0" max="18">
                                            @error('min_age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="max_age" class="form-label">Maximum Age</label>
                                            <input type="number" class="form-control @error('max_age') is-invalid @enderror" 
                                                   id="max_age" name="max_age" value="{{ old('max_age') }}" min="1">
                                            @error('max_age')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                                <div class="text-end">
                                    <a href="{{ route('dashboard.tickets.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Create Ticket</button>
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
