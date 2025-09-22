@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Inquiry">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.index') }}">Inquiries</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.show', $inquiry) }}">#{{ $inquiry->id }}</a></li>
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
                            <h5>Edit Inquiry #{{ $inquiry->id }}</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.inquiries.update', $inquiry) }}" method="POST">
                                @csrf
                                @method('PUT')
                                @if(!admin()->hasRole(['Reservation', 'Operation']))
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="guest_name" class="form-label">Guest Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                                       id="guest_name" name="guest_name" value="{{ old('guest_name', $inquiry->guest_name) }}" required>
                                                @error('guest_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                       id="email" name="email" value="{{ old('email', $inquiry->email) }}" required>
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
                                                       id="phone" name="phone" value="{{ old('phone', $inquiry->phone) }}" required>
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fa fa-lock"></i>
                                        <strong>Access Restricted:</strong> You cannot edit personal guest information with your current role.
                                    </div>
                                    
                                    <!-- Hidden fields to maintain form data -->
                                    <input type="hidden" name="guest_name" value="{{ $inquiry->guest_name }}">
                                    <input type="hidden" name="email" value="{{ $inquiry->email }}">
                                    <input type="hidden" name="phone" value="{{ $inquiry->phone }}">
                                @endif
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="arrival_date" class="form-label">Arrival Date</label>
                                            <input type="date" class="form-control @error('arrival_date') is-invalid @enderror" 
                                                   id="arrival_date" name="arrival_date" value="{{ old('arrival_date', $inquiry->arrival_date?->format('Y-m-d')) }}">
                                            @error('arrival_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="departure_date" class="form-label">Departure Date</label>
                                            <input type="date" class="form-control @error('departure_date') is-invalid @enderror" 
                                                   id="departure_date" name="departure_date" value="{{ old('departure_date', $inquiry->departure_date?->format('Y-m-d')) }}">
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
                                                   id="number_pax" name="number_pax" value="{{ old('number_pax', $inquiry->number_pax) }}" min="1">
                                            @error('number_pax')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tour_name" class="form-label">Tour Name</label>
                                            <input type="text" class="form-control @error('tour_name') is-invalid @enderror" 
                                                   id="tour_name" name="tour_name" value="{{ old('tour_name', $inquiry->tour_name) }}">
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
                                                   id="nationality" name="nationality" value="{{ old('nationality', $inquiry->nationality) }}">
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
                                                    <option value="{{ $value }}" {{ old('status', $inquiry->status->value) == $value ? 'selected' : '' }}>
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
                                           id="subject" name="subject" value="{{ old('subject', $inquiry->subject) }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                @if(admin()->can('inquiries.edit') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="mb-3">
                                                <i class="fa fa-users"></i> User Assignments
                                                <small class="text-muted">(Optional - Assign users to specific roles)</small>
                                            </h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="assigned_reservation_id" class="form-label">Reservation User</label>
                                                <select class="form-control @error('assigned_reservation_id') is-invalid @enderror" 
                                                        id="assigned_reservation_id" name="assigned_reservation_id">
                                                    <option value="">Select Reservation User</option>
                                                    @if(isset($usersByRole['Reservation']))
                                                        @foreach($usersByRole['Reservation'] as $user)
                                                            <option value="{{ $user->id }}" {{ old('assigned_reservation_id', $inquiry->assigned_reservation_id) == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('assigned_reservation_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="assigned_operator_id" class="form-label">Operator User</label>
                                                <select class="form-control @error('assigned_operator_id') is-invalid @enderror" 
                                                        id="assigned_operator_id" name="assigned_operator_id">
                                                    <option value="">Select Operator User</option>
                                                    @if(isset($usersByRole['Operation']))
                                                        @foreach($usersByRole['Operation'] as $user)
                                                            <option value="{{ $user->id }}" {{ old('assigned_operator_id', $inquiry->assigned_operator_id) == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('assigned_operator_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="assigned_admin_id" class="form-label">Admin User</label>
                                                <select class="form-control @error('assigned_admin_id') is-invalid @enderror" 
                                                        id="assigned_admin_id" name="assigned_admin_id">
                                                    <option value="">Select Admin User</option>
                                                    @if(isset($usersByRole['Admin']))
                                                        @foreach($usersByRole['Admin'] as $user)
                                                            <option value="{{ $user->id }}" {{ old('assigned_admin_id', $inquiry->assigned_admin_id) == $user->id ? 'selected' : '' }}>
                                                                {{ $user->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('assigned_admin_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Legacy assignment field for backward compatibility -->
                                    <div class="mb-3">
                                        <label for="assigned_to" class="form-label">General Assignment</label>
                                        <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                                id="assigned_to" name="assigned_to">
                                            <option value="">Select User (General Assignment)</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $inquiry->assigned_to) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} - {{ $user->roles->first()?->name ?? 'No Role' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('assigned_to')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">This is a general assignment field for backward compatibility.</small>
                                    </div>
                                @endif


                                <div class="text-end">
                                    <a href="{{ route('dashboard.inquiries.show', $inquiry) }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Inquiry</button>
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







