@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Inquiry">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.index') }}">Inquiries</a></li>
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
                            <h5>Create New Inquiry</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.inquiries.store') }}" method="POST">
                                @csrf
                                @if(!admin()->hasRole(['Reservation', 'Operation']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="guest_name" class="form-label">Guest Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                                       id="guest_name" name="guest_name" value="{{ old('guest_name') }}" required>
                                                @error('guest_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                       id="phone" name="phone" value="{{ old('phone') }}" required>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fa fa-lock"></i>
                                        <strong>Access Restricted:</strong> You cannot create inquiries with personal guest information using your current role.
                                        <br><small>Please contact an Administrator, Admin, or Sales user to create inquiries.</small>
                                    </div>
                                    
                                    <!-- Hidden fields with default values -->
                                    <input type="hidden" name="guest_name" value="Restricted Access">
                                    <input type="hidden" name="email" value="restricted@example.com">
                                    <input type="hidden" name="phone" value="000-000-0000">
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="arrival_date" class="form-label">Arrival Date</label>
                                            <input type="date" class="form-control @error('arrival_date') is-invalid @enderror" 
                                                   id="arrival_date" name="arrival_date" value="{{ old('arrival_date') }}">
                                            @error('arrival_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departure_date" class="form-label">Departure Date</label>
                                            <input type="date" class="form-control @error('departure_date') is-invalid @enderror" 
                                                   id="departure_date" name="departure_date" value="{{ old('departure_date') }}">
                                            @error('departure_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="number_pax" class="form-label">Number of Pax</label>
                                            <input type="number" class="form-control @error('number_pax') is-invalid @enderror" 
                                                   id="number_pax" name="number_pax" value="{{ old('number_pax') }}" min="1">
                                            @error('number_pax')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tour_name" class="form-label">Tour Name</label>
                                            <input type="text" class="form-control @error('tour_name') is-invalid @enderror" 
                                                   id="tour_name" name="tour_name" value="{{ old('tour_name') }}">
                                            @error('tour_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nationality" class="form-label">Nationality</label>
                                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                                   id="nationality" name="nationality" value="{{ old('nationality') }}">
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status') == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(admin()->can('inquiries.edit') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label">Assign to User</label>
                                        <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                                id="assigned_to" name="assigned_to">
                                            <option value="">Select User</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} - {{ $user->roles->first()?->name ?? 'No Role' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif


                                <div class="text-end">
                                    <a href="{{ route('dashboard.inquiries.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Create Inquiry</button>
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







