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
                            <h5>{{ $inquiry->inquiry_id ?? 'Inquiry #' . $inquiry->id }}</h5>
                            
                            <div class="card-header-right">
                                <div class="btn-group">
                                    @if(admin()->can('inquiries.edit'))
                                        <a href="{{ route('dashboard.inquiries.edit', $inquiry) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    @endif
                                    @if($inquiry->status->value !== 'confirmed' && admin()->can('inquiries.confirm'))
                                        <a href="{{ route('dashboard.inquiries.confirm-form', $inquiry) }}" class="btn btn-success btn-sm ms-1">
                                            <i class="fa fa-check"></i> Confirm with Payment
                                        </a>
                                        <form action="{{ route('dashboard.inquiries.confirm', $inquiry) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm ms-1" 
                                                    onclick="return confirm('Are you sure you want to confirm this inquiry without payment details?')">
                                                <i class="fa fa-check"></i> Quick Confirm
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    @if(!admin()->hasRole(['Reservation', 'Operator']))
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Guest Name:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->guest_name }}</p>
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
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Access Restricted:</strong> Personal guest information is not available for your role.
                                        </div>
                                    @endif
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Arrival Date:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->arrival_date?->format('M d, Y') ?? 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Departure Date:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->departure_date?->format('M d, Y') ?? 'Not specified' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Number of Pax:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->number_pax ?? 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tour Name:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->tour_name ?? 'Not specified' }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nationality:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->nationality ?? 'Not specified' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Status:</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge badge-{{ $inquiry->status->value === 'pending' ? 'warning' : ($inquiry->status->value === 'confirmed' ? 'success' : 'danger') }}">
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
{{-- tour_itinerary section --}}
                                    
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tour Itinerary:</label>
                                            <div class="border p-3 rounded bg-[#8b5cf6] ">
                                                <div id="tour-itinerary-display" class="tour-itinerary-content fw-bold" style="white-space: pre-wrap; line-height: 1.6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                                                    @if($inquiry->tour_itinerary)
                                                        {{ $inquiry->tour_itinerary }}
                                                    @else
                                                        <em class="text-muted">No tour itinerary added yet.</em>
                                                    @endif
                                                </div>
                                                <div id="tour-itinerary-edit" style="display: none;">
                                                    <textarea id="tour-itinerary-textarea" class="form-control" rows="8" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $inquiry->tour_itinerary }}</textarea>
                                                    <div class="mt-2">
                                                        <button type="button" id="save-tour-itinerary" class="btn btn-success btn-sm">
                                                            <i class="fa fa-save"></i> Save
                                                        </button>
                                                        <button type="button" id="cancel-tour-itinerary" class="btn btn-secondary btn-sm ms-2">
                                                            <i class="fa fa-times"></i> Cancel
                                                        </button>
                                                    </div>
                                                </div>
                                                @if(admin()->hasRole(['Sales']))
                                                    <div class="mt-2">
                                                        <button type="button" id="edit-tour-itinerary" class="btn btn-outline-primary btn-sm">
                                                            <i class="fa fa-edit"></i> Edit Itinerary
                                                        </button>
                                                    </div>
                                                @elseif(!admin()->hasRole(['Sales']))
                                                    <div class="mt-2">
                                                        <small class="text-muted">
                                                            <i class="fa fa-lock"></i> Only Sales role can edit this content
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    

                                    {{-- Payment Information section hidden as requested --}}
                                    {{-- 
                                    @if($inquiry->status->value === 'confirmed' && ($inquiry->total_amount || $inquiry->paid_amount || $inquiry->payment_method))
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Payment Information:</label>
                                            <div class="border p-3 rounded bg-dark">
                                                <div class="row">
                                                    @if($inquiry->total_amount)
                                                        <div class="col-md-4">
                                                            <strong>Total Amount:</strong><br>
                                                            <span class="text-primary">${{ number_format($inquiry->total_amount, 2) }}</span>
                                                        </div>
                                                    @endif
                                                    @if($inquiry->paid_amount)
                                                        <div class="col-md-4">
                                                            <strong>Paid Amount (Deposit):</strong><br>
                                                            <span class="text-success">${{ number_format($inquiry->paid_amount, 2) }}</span>
                                                        </div>
                                                    @endif
                                                    @if($inquiry->remaining_amount)
                                                        <div class="col-md-4">
                                                            <strong>Remaining Amount:</strong><br>
                                                            <span class="text-warning">${{ number_format($inquiry->remaining_amount, 2) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($inquiry->payment_method)
                                                    <div class="mt-2">
                                                        <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $inquiry->payment_method)) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    --}}

                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            @if(admin()->can('inquiries.edit') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operator']))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">User Assignments:</label>
                                                    <div class="form-control-plaintext">
                                                        @php
                                                            $assignedUsers = $inquiry->getAllAssignedUsers();
                                                        @endphp
                                                        
                                                        @if(count($assignedUsers) > 0)
                                                            @foreach($assignedUsers as $assignment)
                                                                <div class="mb-2 p-2 border rounded" style="background-color: #f8f9fa; border-color: #dee2e6 !important;">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            @if($assignment['type'] === 'user')
                                                                                <strong>{{ $assignment['user']->name }}</strong>
                                                                                <br>
                                                                                <small class="text-muted">
                                                                                    <i class="fa fa-user"></i> 
                                                                                    {{ $assignment['role'] }}
                                                                                </small>
                                                                            @elseif($assignment['type'] === 'resource')
                                                                                <strong>{{ $assignment['resource']->resource_name }}</strong>
                                                                                <br>
                                                                                <small class="text-muted">
                                                                                    <i class="fa fa-tag"></i> 
                                                                                    {{ $assignment['role'] }}
                                                                                    @if($assignment['added_by'])
                                                                                        - Added by {{ $assignment['added_by']->name }}
                                                                                    @endif
                                                                                </small>
                                                                            @endif
                                                                        </div>
                                                                        <span class="badge badge-primary">{{ $assignment['role'] }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fa fa-info-circle"></i> No users or resources assigned
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Created At:</label>
                                                <p class="form-control-plaintext">{{ $inquiry->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                            
                                            @if($inquiry->confirmed_at && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operator'])))
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Confirmed At:</label>
                                                    <p class="form-control-plaintext">{{ $inquiry->confirmed_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            @endif
                                            
                                            
                                            @if($inquiry->client && (admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operator', 'Finance'])))
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
            
            <!-- Resources Management Section -->
            <div class="row mt-4">
                <div class="col-sm-12">
                    @include('dashboard.inquiries.partials.resources-improved')
                </div>
            </div>
            
            <!-- Chat Section -->
            @if(admin()->can('inquiries.show') || admin()->hasRole(['Administrator', 'Admin', 'Sales', 'Reservation', 'Operator']))
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButton = document.getElementById('edit-tour-itinerary');
    const saveButton = document.getElementById('save-tour-itinerary');
    const cancelButton = document.getElementById('cancel-tour-itinerary');
    const displayDiv = document.getElementById('tour-itinerary-display');
    const editDiv = document.getElementById('tour-itinerary-edit');
    const textarea = document.getElementById('tour-itinerary-textarea');
    
    if (editButton) {
        editButton.addEventListener('click', function() {
            displayDiv.style.display = 'none';
            editDiv.style.display = 'block';
            editButton.style.display = 'none';
            textarea.focus();
        });
    }
    
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            displayDiv.style.display = 'block';
            editDiv.style.display = 'none';
            editButton.style.display = 'inline-block';
            // Reset textarea to original content
            const originalContent = displayDiv.textContent.trim();
            textarea.value = originalContent === 'No tour itinerary added yet.' ? '' : originalContent;
        });
    }
    
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            const tourItinerary = textarea.value;
            
            // Show loading state
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
            
            // Send AJAX request
            fetch('{{ route("dashboard.inquiries.update-tour-itinerary", $inquiry) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tour_itinerary: tourItinerary
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display content
                    if (data.tour_itinerary && data.tour_itinerary.trim()) {
                        displayDiv.textContent = data.tour_itinerary;
                    } else {
                        displayDiv.innerHTML = '<em class="text-muted">No tour itinerary added yet.</em>';
                    }
                    
                    // Switch back to display mode
                    displayDiv.style.display = 'block';
                    editDiv.style.display = 'none';
                    editButton.style.display = 'inline-block';
                    
                    // Show success message
                    showAlert('success', data.message);
                } else {
                    showAlert('error', data.message || 'Failed to update tour itinerary');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred while updating tour itinerary');
            })
            .finally(() => {
                // Reset button state
                saveButton.disabled = false;
                saveButton.innerHTML = '<i class="fa fa-save"></i> Save';
            });
        });
    }
    
    function showAlert(type, message) {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Insert at the top of the page body
        const pageBody = document.querySelector('.page-body');
        if (pageBody) {
            pageBody.insertBefore(alertDiv, pageBody.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    }
});
</script>
@endpush





