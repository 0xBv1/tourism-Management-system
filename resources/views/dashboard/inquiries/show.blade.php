@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Inquiry Details">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.inquiries.index') }}">Inquiries</a></li>
            <li class="breadcrumb-item active">#{{ $inquiry->id }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Inquiry #{{ $inquiry->id }}</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user-tag"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                            <div class="card-header-right">
                                <div class="btn-group">
                                    @if(admin()->can('inquiries.edit'))
                                        <a href="{{ route('dashboard.inquiries.edit', $inquiry) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($inquiry->status->value !== 'confirmed' && admin()->can('inquiries.confirm') && $inquiry->user1_id && $inquiry->user2_id && ($inquiry->user1_id === auth()->id() || $inquiry->user2_id === auth()->id()) && !$inquiry->hasUserConfirmed(auth()->id()))
                                        <form action="{{ route('dashboard.inquiries.confirm', $inquiry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ms-1" 
                                                    onclick="return confirm('Are you sure you want to confirm this inquiry?')">
                                                <i class="fa fa-check"></i> Confirm
                                            </button>
                                        </form>
                                    @elseif($inquiry->status->value !== 'confirmed' && $inquiry->user1_id && $inquiry->user2_id && $inquiry->hasUserConfirmed(auth()->id()))
                                        <span class="badge badge-success badge-sm ms-1">
                                            <i class="fa fa-check"></i> You Confirmed
                                        </span>
                                    @elseif($inquiry->status->value !== 'confirmed' && (!$inquiry->user1_id || !$inquiry->user2_id))
                                        <span class="badge badge-warning badge-sm ms-1">
                                            <i class="fa fa-exclamation-triangle"></i> No Confirmation Users
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Name:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Email:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Phone:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->phone }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status:</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge badge-{{ $inquiry->status->value === 'pending' ? 'warning' : ($inquiry->status->value === 'confirmed' ? 'success' : ($inquiry->status->value === 'cancelled' ? 'danger' : 'info')) }}">
                                                        {{ ucfirst($inquiry->status->value) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Subject:</label>
                                        <p class="form-control-plaintext">{{ $inquiry->subject }}</p>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Message:</label>
                                        <div class="border p-3 rounded">
                                            {{ $inquiry->message }}
                                        </div>
                                    </div>

                                    @if($inquiry->admin_notes && admin()->can('inquiries.edit'))
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Admin Notes:</label>
                                            <div class="border p-3 rounded bg-light">
                                                {{ $inquiry->admin_notes }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            @if(admin()->can('inquiries.edit') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Assigned To:</label>
                                                    <p class="form-control-plaintext">
                                                        {{ $inquiry->assignedUser?->name ?? 'Unassigned' }}
                                                    </p>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Created At:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            
                                            @if($inquiry->confirmed_at && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation'])))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Confirmed At:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->confirmed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($inquiry->completed_at && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation'])))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Completed At:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->completed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($inquiry->client && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation', 'Finance'])))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Client:</label>
                                                    <p class="form-control-plaintext">
                                                        <a href="#" class="text-decoration-none">{{ $inquiry->client->name }}</a>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- User Confirmation Status Section -->
                            @if($inquiry->user1_id && $inquiry->user2_id)
                                <div class="row mt-4">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Confirmation Status</h6>
                                            </div>
                                            <div class="card-body">
                                                @php
                                                    $confirmationStatus = $inquiry->getConfirmationStatus();
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                @if($confirmationStatus['user1_confirmed'])
                                                                    <i class="fa fa-check-circle text-success fa-2x"></i>
                                                                @else
                                                                    <i class="fa fa-clock text-warning fa-2x"></i>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1">{{ $confirmationStatus['user1_name'] }}</h6>
                                                                @if($confirmationStatus['user1_confirmed'])
                                                                    <small class="text-success">
                                                                        <i class="fa fa-check"></i> Confirmed
                                                                        @if($confirmationStatus['user1_confirmed_at'])
                                                                            - {{ $confirmationStatus['user1_confirmed_at']->format('M d, Y H:i') }}
                                                                        @endif
                                                                    </small>
                                                                @else
                                                                    <small class="text-warning">
                                                                        <i class="fa fa-clock"></i> Pending
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div class="me-3">
                                                                @if($confirmationStatus['user2_confirmed'])
                                                                    <i class="fa fa-check-circle text-success fa-2x"></i>
                                                                @else
                                                                    <i class="fa fa-clock text-warning fa-2x"></i>
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-1">{{ $confirmationStatus['user2_name'] }}</h6>
                                                                @if($confirmationStatus['user2_confirmed'])
                                                                    <small class="text-success">
                                                                        <i class="fa fa-check"></i> Confirmed
                                                                        @if($confirmationStatus['user2_confirmed_at'])
                                                                            - {{ $confirmationStatus['user2_confirmed_at']->format('M d, Y H:i') }}
                                                                        @endif
                                                                    </small>
                                                                @else
                                                                    <small class="text-warning">
                                                                        <i class="fa fa-clock"></i> Pending
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($confirmationStatus['fully_confirmed'])
                                                    <div class="alert alert-success">
                                                        <i class="fa fa-check-circle"></i>
                                                        <strong>Fully Confirmed!</strong> Both users have confirmed this inquiry.
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        <i class="fa fa-info-circle"></i>
                                                        <strong>Waiting for Confirmations:</strong> Both users must confirm before the inquiry is fully confirmed.
                                                    </div>
                                                    
                                                    <!-- User Confirmation Button -->
                                                    @if(($inquiry->user1_id === auth()->id() || $inquiry->user2_id === auth()->id()) && !$inquiry->hasUserConfirmed(auth()->id()))
                                                        <div class="text-center">
                                                            <form action="{{ route('dashboard.inquiries.confirm', $inquiry) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-lg" 
                                                                        onclick="return confirm('Are you sure you want to confirm this inquiry?')">
                                                                    <i class="fa fa-check"></i> Confirm This Inquiry
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($inquiry->hasUserConfirmed(auth()->id()))
                                                        <div class="text-center">
                                                            <span class="badge badge-success badge-lg">
                                                                <i class="fa fa-check"></i> You have confirmed this inquiry
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- No Confirmation Users Set -->
                                <div class="row mt-4">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6>Confirmation Status</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-exclamation-triangle"></i>
                                                    <strong>Confirmation Users Not Set:</strong> 
                                                    No confirmation users have been assigned to this inquiry yet.
                                                    @if(admin()->can('inquiries.edit') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
                                                        <br><br>
                                                        <a href="{{ route('dashboard.inquiries.edit', $inquiry) }}" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-edit"></i> Assign Confirmation Users
                                                        </a>
                                                    @else
                                                        <br><br>
                                                        Please contact an administrator to assign confirmation users.
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Section -->
            @if(admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operation']))
                <div class="row mt-4">
                    <div class="col-sm-12">
                        @include('dashboard.inquiries.partials.chat')
                    </div>
                </div>
            @endif
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection





