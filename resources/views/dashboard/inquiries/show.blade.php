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
                            <div class="card-header-right">
                                <div class="btn-group">
                                    <a href="{{ route('dashboard.inquiries.edit', $inquiry) }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    @if($inquiry->status->value !== 'confirmed')
                                        <form action="{{ route('dashboard.inquiries.confirm', $inquiry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ms-1" 
                                                    onclick="return confirm('Are you sure you want to confirm this inquiry?')">
                                                <i class="fa fa-check"></i> Confirm
                                            </button>
                                        </form>
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

                                    @if($inquiry->admin_notes)
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
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Assigned To:</label>
                                                <p class="form-control-plaintext">
                                                    {{ $inquiry->assignedUser?->name ?? 'Unassigned' }}
                                                </p>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Created At:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            
                                            @if($inquiry->confirmed_at)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Confirmed At:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->confirmed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($inquiry->completed_at)
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Completed At:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->completed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            @endif
                                            
                                            @if($inquiry->client)
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

