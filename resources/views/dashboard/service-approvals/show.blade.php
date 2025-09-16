@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Service Approval Details">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard.service-approvals.index') }}">Service Approvals</a>
            </li>
            <li class="breadcrumb-item active">Details</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>Service Approval #{{ $serviceApproval->id }}</h5>
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Approval Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Approval Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                @if(auth()->user()->can('service-approvals.update') || auth()->user()->can('service-approvals.approve') || auth()->user()->can('service-approvals.reject'))
                                                    <div class="d-flex align-items-center">
                                                        <select id="statusSelect" class="form-select form-select-sm me-2" style="width: auto; min-width: 120px;">
                                                            <option value="pending" {{ $serviceApproval->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $serviceApproval->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="rejected" {{ $serviceApproval->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                        </select>
                                                        <button type="button" id="updateStatusBtn" class="btn btn-sm btn-primary">
                                                            <i class="fa fa-save"></i> Update
                                                        </button>
                                                        <span id="statusUpdateSpinner" class="ms-2" style="display: none;">
                                                            <i class="fa fa-spinner fa-spin text-primary"></i>
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-{{ $serviceApproval->status_color }}">
                                                        {{ $serviceApproval->status_label }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Service Type:</strong></td>
                                            <td>{{ $serviceApproval->service_type_label }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Submitted:</strong></td>
                                            <td>{{ $serviceApproval->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @if($serviceApproval->isApproved())
                                            <tr>
                                                <td><strong>Approved By:</strong></td>
                                                <td>{{ $serviceApproval->approvedBy->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Approved At:</strong></td>
                                                <td>{{ $serviceApproval->approved_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        @endif
                                        @if($serviceApproval->isRejected())
                                            <tr>
                                                <td><strong>Rejected By:</strong></td>
                                                <td>{{ $serviceApproval->approvedBy->name ?? 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Rejected At:</strong></td>
                                                <td>{{ $serviceApproval->rejected_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Reason:</strong></td>
                                                <td>{{ $serviceApproval->rejection_reason }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </div>

                                <!-- Supplier Information -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Supplier Information</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Company:</strong></td>
                                            <td>{{ $serviceApproval->supplier->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact:</strong></td>
                                            <td>{{ $serviceApproval->supplier->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $serviceApproval->supplier->user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $serviceApproval->supplier->phone ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Service Details -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h6 class="mb-3">Service Details</h6>
                                    <div class="card">
                                        <div class="card-body">
                                            @if($serviceApproval->service)
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Name:</strong> 
                                                            @if($serviceApproval->service)
                                                                {{ $serviceApproval->service->name ?? $serviceApproval->service->title ?? 'N/A' }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </p>
                                                        <p><strong>Type:</strong> {{ $serviceApproval->service_type_label }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>ID:</strong> {{ $serviceApproval->service_id }}</p>
                                                        <p><strong>Service Status:</strong> 
                                                            @if($serviceApproval->service)
                                                                @if(isset($serviceApproval->service->approved))
                                                                    @if($serviceApproval->service->approved)
                                                                        <span class="badge bg-success">Service Approved</span>
                                                                    @else
                                                                        <span class="badge bg-warning">Service Pending</span>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-secondary">Unknown</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-danger">Not Found</span>
                                                            @endif
                                                        </p>
                                                        <p><strong>Approval Request:</strong> 
                                                            <span class="badge bg-{{ $serviceApproval->status_color }}">
                                                                {{ $serviceApproval->status_label }}
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                                
                                                <!-- Additional service details can be added here based on service type -->
                                                @if($serviceApproval->service_type === 'hotel' && $serviceApproval->service)
                                                    <div class="mt-3">
                                                        <p><strong>Description:</strong> {{ $serviceApproval->service->description ?? 'N/A' }}</p>
                                                        <p><strong>Address:</strong> {{ $serviceApproval->service->address ?? 'N/A' }}</p>
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-muted">Service details not available.</p>
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

    <!-- Reject Modal -->
    @include('dashboard.service-approvals.partials.reject-modal')
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusUpdateModalLabel">Update Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="statusUpdateForm">
                        <div class="mb-3">
                            <label for="modalStatusSelect" class="form-label">New Status</label>
                            <select id="modalStatusSelect" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="mb-3" id="rejectionReasonGroup" style="display: none;">
                            <label for="rejectionReason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea id="rejectionReason" class="form-control" rows="3" placeholder="Please provide a detailed reason for rejection (minimum 10 characters)"></textarea>
                            <div class="form-text">This reason will be stored and visible to the supplier.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmStatusUpdate">
                        <i class="fa fa-save"></i> Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <style>
        .status-select-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .status-select {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        .status-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .status-select option[value="pending"] {
            color: #856404;
            background-color: #fff3cd;
        }
        
        .status-select option[value="approved"] {
            color: #155724;
            background-color: #d4edda;
        }
        
        .status-select option[value="rejected"] {
            color: #721c24;
            background-color: #f8d7da;
        }
        
        .btn-warning {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background-color: #e0a800;
            border-color: #d39e00;
            color: #212529;
        }
    </style>
    
    <script>
        function approveService() {
            if (confirm('Are you sure you want to approve this service?')) {
                fetch('{{ route('dashboard.service-approvals.approve', $serviceApproval->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    } else if (response.ok) {
                        window.location.href = '{{ route('dashboard.service-approvals.index') }}';
                    } else {
                        alert('Failed to approve service. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        // Handle status update
        $(document).ready(function() {
            $('#updateStatusBtn').on('click', function() {
                var select = $('#statusSelect');
                var newStatus = select.val();
                var currentStatus = '{{ $serviceApproval->status }}';
                
                // Don't update if status hasn't changed
                if (newStatus === currentStatus) {
                    toastr.info('Status is already set to ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    return;
                }
                
                // Set the modal status select to current value
                $('#modalStatusSelect').val(newStatus);
                
                // Show/hide rejection reason field based on status
                if (newStatus === 'rejected') {
                    $('#rejectionReasonGroup').show();
                    $('#rejectionReason').prop('required', true);
                } else {
                    $('#rejectionReasonGroup').hide();
                    $('#rejectionReason').prop('required', false);
                }
                
                // Show the modal
                $('#statusUpdateModal').modal('show');
            });
            
            // Handle modal status change
            $('#modalStatusSelect').on('change', function() {
                var newStatus = $(this).val();
                
                if (newStatus === 'rejected') {
                    $('#rejectionReasonGroup').show();
                    $('#rejectionReason').prop('required', true);
                } else {
                    $('#rejectionReasonGroup').hide();
                    $('#rejectionReason').prop('required', false);
                }
            });
            
            // Handle confirm status update
            $('#confirmStatusUpdate').on('click', function() {
                var modalSelect = $('#modalStatusSelect');
                var newStatus = modalSelect.val();
                var currentStatus = '{{ $serviceApproval->status }}';
                var rejectionReason = $('#rejectionReason').val();
                
                // Validate rejection reason if needed
                if (newStatus === 'rejected' && (!rejectionReason || rejectionReason.trim().length < 10)) {
                    toastr.error('Rejection reason is required and must be at least 10 characters long.');
                    return;
                }
                
                // Show loading state
                var btn = $(this);
                var originalText = btn.html();
                btn.prop('disabled', true);
                btn.html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                
                // Prepare data
                var data = {
                    status: newStatus,
                    _token: '{{ csrf_token() }}'
                };
                
                if (newStatus === 'rejected') {
                    data.rejection_reason = rejectionReason.trim();
                }
                
                // Send AJAX request
                $.ajax({
                    url: '{{ route('dashboard.service-approvals.update-status', $serviceApproval->id) }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            
                            // Close modal
                            $('#statusUpdateModal').modal('hide');
                            
                            // Update the page to reflect changes
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message || 'Failed to update status');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            message = 'You do not have permission to update the status. Please contact your administrator.';
                        } else if (xhr.status === 422) {
                            message = 'Validation error. Please check your input and try again.';
                        }
                        toastr.error(message);
                        
                        // Log the error for debugging
                        console.error('Status update error:', xhr);
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                        btn.html(originalText);
                    }
                });
            });
            
            // Handle status select change
            $('#statusSelect').on('change', function() {
                var newStatus = $(this).val();
                var currentStatus = '{{ $serviceApproval->status }}';
                
                if (newStatus === currentStatus) {
                    $('#updateStatusBtn').removeClass('btn-warning').addClass('btn-primary');
                } else {
                    $('#updateStatusBtn').removeClass('btn-primary').addClass('btn-warning');
                }
            });
            
            // Reset modal when closed
            $('#statusUpdateModal').on('hidden.bs.modal', function() {
                $('#rejectionReason').val('');
                $('#rejectionReasonGroup').hide();
                $('#rejectionReason').prop('required', false);
            });
        });
    </script>
@endpush
