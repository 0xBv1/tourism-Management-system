@if(admin()->hasRole(['Sales' ,'Finance']) || Gate::allows('manage-inquiry-resources'))
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            @if(Gate::allows('manage-inquiry-resources'))
                <i class="fas fa-cogs me-2"></i>Resources Management
            @else
                <i class="fas fa-eye me-2"></i>Assigned Resources
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if(Gate::allows('manage-inquiry-resources'))
        <!-- Resource Type Navigation -->
        <ul class="nav nav-tabs mb-4" id="resourceTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hotel-tab" data-bs-toggle="tab" data-bs-target="#hotel" type="button" role="tab" aria-controls="hotel" aria-selected="true">
                    <i class="fas fa-hotel me-1"></i>Hotels
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button" role="tab" aria-controls="vehicle" aria-selected="false">
                    <i class="fas fa-car me-1"></i>Vehicles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button" role="tab" aria-controls="guide" aria-selected="false">
                    <i class="fas fa-user-tie me-1"></i>Guides
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="representative-tab" data-bs-toggle="tab" data-bs-target="#representative" type="button" role="tab" aria-controls="representative" aria-selected="false">
                    <i class="fas fa-handshake me-1"></i>Representatives
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="extra-tab" data-bs-toggle="tab" data-bs-target="#extra" type="button" role="tab" aria-controls="extra" aria-selected="false">
                    <i class="fas fa-plus-circle me-1"></i>Extra Services
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="resourceTabContent">
            <!-- Hotel Tab -->
            <div class="tab-pane fade show active" id="hotel" role="tabpanel" aria-labelledby="hotel-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="hotel_select" class="form-label">Select Hotel</label>
                        <select class="form-select" id="hotel_select" name="hotel_id">
                            <option value="">Choose a hotel...</option>
                            @foreach($availableResources['hotels'] as $hotel)
                                <option value="{{ $hotel->id }}">
                                    {{ $hotel->name }}@if($hotel->city) ({{ $hotel->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addHotelBtn" >
                                <i class="fas fa-plus me-1"></i>Add Hotel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vehicle Tab -->
            <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="vehicle_select" class="form-label">Select Vehicle</label>
                        <select class="form-select" id="vehicle_select" name="vehicle_id">
                            <option value="">Choose a vehicle...</option>
                            @foreach($availableResources['vehicles'] as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->name }}@if($vehicle->type) - {{ $vehicle->type }}@endif @if($vehicle->city) ({{ $vehicle->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addVehicleBtn" >
                                <i class="fas fa-plus me-1"></i>Add Vehicle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guide Tab -->
            <div class="tab-pane fade" id="guide" role="tabpanel" aria-labelledby="guide-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="guide_select" class="form-label">Select Guide</label>
                        <select class="form-select" id="guide_select" name="guide_id">
                            <option value="">Choose a guide...</option>
                            @foreach($availableResources['guides'] as $guide)
                                <option value="{{ $guide->id }}">
                                    {{ $guide->name }}@if($guide->city) ({{ $guide->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addGuideBtn" >
                                <i class="fas fa-plus me-1"></i>Add Guide
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Representative Tab -->
            <div class="tab-pane fade" id="representative" role="tabpanel" aria-labelledby="representative-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="representative_select" class="form-label">Select Representative</label>
                        <select class="form-select" id="representative_select" name="representative_id">
                            <option value="">Choose a representative...</option>
                            @foreach($availableResources['representatives'] as $representative)
                                <option value="{{ $representative->id }}">
                                    {{ $representative->name }}@if($representative->city) ({{ $representative->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addRepresentativeBtn" >
                                <i class="fas fa-plus me-1"></i>Add Representative
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Extra Services Tab -->
            <div class="tab-pane fade" id="extra" role="tabpanel" aria-labelledby="extra-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="extra_select" class="form-label">Select Extra Service</label>
                        <select class="form-select" id="extra_select" name="extra_id">
                            <option value="">Choose an extra service...</option>
                            @foreach($availableResources['extras'] as $extra)
                                <option value="{{ $extra->id }}">
                                    {{ $extra->name }}@if($extra->category) - {{ $extra->category }}@endif - {{ $extra->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addExtraBtn" >
                                <i class="fas fa-plus me-1"></i>Add Service
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Separator -->
        <hr class="my-4">

        <!-- Resources Table -->
        <div class="table-responsive">
            <h6 class="mb-3">
                <i class="fas fa-list me-2"></i>Current Resources
            </h6>
            @if(!Gate::allows('manage-inquiry-resources'))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>View Only:</strong> This shows resources that have been assigned to this inquiry by Operations staff. 
                    You can see which resources are available and who added them.
                </div>
            @endif
            <table class="table table-striped" id="resourcesTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Resource Name</th>
                        <th>Added By</th>
                        <th>Added At</th>
                        @if(Gate::allows('manage-inquiry-resources'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiry->resources as $resource)
                        <tr data-resource-id="{{ $resource->id }}">
                            <td>
                                <span class="badge bg-{{ $resource->resource_type === 'hotel' ? 'primary' : ($resource->resource_type === 'vehicle' ? 'success' : ($resource->resource_type === 'guide' ? 'info' : ($resource->resource_type === 'representative' ? 'warning' : 'secondary'))) }}">
                                    {{ ucfirst($resource->resource_type) }}
                                </span>
                            </td>
                            <td>{{ $resource->resource_name }}</td>
                            <td>
                                <span class="badge bg-info">{{ $resource->addedBy->name }}</span>
                                @if($resource->addedBy->hasRole(['Operator', 'Admin', 'Administrator']))
                                    <small class="text-muted d-block">Operator</small>
                                @endif
                            </td>
                            <td>{{ $resource->created_at->format('M d, Y H:i') }}</td>
                            @if(Gate::allows('manage-inquiry-resources'))
                                <td>
                                    <button class="btn btn-sm btn-outline-danger remove-resource" 
                                            data-resource-id="{{ $resource->id }}"
                                            data-resource-name="{{ $resource->resource_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Gate::allows('manage-inquiry-resources') ? '5' : '4' }}" class="text-center text-muted">
                                <i class="fas fa-info-circle me-2"></i>No resources added yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    padding: 1.5rem;
    background-color: #fff;
}

.form-select:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.alert {
    border-radius: 0.375rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endpush

@if(Gate::allows('manage-inquiry-resources'))
<script>
// Wait for jQuery to be available
function waitForJQuery() {
    if (typeof $ !== 'undefined') {
        console.log('=== RESOURCES SCRIPT LOADED ===');
        console.log('jQuery version:', $.fn.jquery);
        
        $(document).ready(function() {
            const inquiryId = {{ $inquiry->id }};
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            console.log('Inquiry ID:', inquiryId);
            console.log('CSRF Token:', csrfToken);
            
            // Test if elements exist
            console.log('Hotel select exists:', $('#hotel_select').length > 0);
            console.log('Hotel button exists:', $('#addHotelBtn').length > 0);
            
            // Handle resource selection changes - Direct approach
            $('#hotel_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addHotelBtn').prop('disabled', !resourceId);
                console.log('Hotel selected:', resourceId, 'Button disabled:', $('#addHotelBtn').prop('disabled'));
            });
            
            $('#vehicle_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addVehicleBtn').prop('disabled', !resourceId);
                console.log('Vehicle selected:', resourceId, 'Button disabled:', $('#addVehicleBtn').prop('disabled'));
            });
            
            $('#guide_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addGuideBtn').prop('disabled', !resourceId);
                console.log('Guide selected:', resourceId, 'Button disabled:', $('#addGuideBtn').prop('disabled'));
            });
            
            $('#representative_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addRepresentativeBtn').prop('disabled', !resourceId);
                console.log('Representative selected:', resourceId, 'Button disabled:', $('#addRepresentativeBtn').prop('disabled'));
            });
            
            $('#extra_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addExtraBtn').prop('disabled', !resourceId);
                console.log('Extra selected:', resourceId, 'Button disabled:', $('#addExtraBtn').prop('disabled'));
            });
            
            // Handle add resource button clicks
            $('#addHotelBtn').on('click', function() {
                addResource('hotel');
            });
            
            $('#addVehicleBtn').on('click', function() {
                addResource('vehicle');
            });
            
            $('#addGuideBtn').on('click', function() {
                addResource('guide');
            });
            
            $('#addRepresentativeBtn').on('click', function() {
                addResource('representative');
            });
            
            $('#addExtraBtn').on('click', function() {
                addResource('extra');
            });
            
            // Function to add a resource
            function addResource(resourceType) {
                const selectId = '#' + resourceType + '_select';
                const buttonId = '#add' + resourceType.charAt(0).toUpperCase() + resourceType.slice(1) + 'Btn';
                const select = $(selectId);
                const button = $(buttonId);
                const resourceId = select.val();
                
                if (!resourceId) {
                    showAlert('error', 'Please select a ' + resourceType + ' first');
                    return;
                }
                
                const formData = {
                    resource_type: resourceType,
                    resource_id: resourceId,
                    _token: csrfToken
                };
                
                const originalText = button.html();
                
                // Show loading state
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
                
                $.ajax({
                    url: `/dashboard/inquiries/${inquiryId}/resources`,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            
                            // Add new row to table
                            const newRow = `
                                <tr data-resource-id="${response.data.id}">
                                    <td>
                                        <span class="badge bg-${getResourceTypeColor(response.data.resource_type)}">
                                            ${response.data.resource_type.charAt(0).toUpperCase() + response.data.resource_type.slice(1)}
                                        </span>
                                    </td>
                                    <td>${response.data.resource_name}</td>
                                    <td>${response.data.added_by}</td>
                                    <td>${new Date(response.data.created_at).toLocaleDateString()}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-danger remove-resource" 
                                                data-resource-id="${response.data.id}"
                                                data-resource-name="${response.data.resource_name}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                            
                            // Remove empty row if exists
                            const emptyRow = $('#resourcesTable tbody tr:first');
                            if (emptyRow.find('td').length === 1) {
                                emptyRow.remove();
                            }
                            
                            // Add new row
                            $('#resourcesTable tbody').prepend(newRow);
                            
                            // Reset the select
                            select.val('');
                            button.prop('disabled', true);
                        } else {
                            showAlert('error', response.message || 'Failed to add resource');
                        }
                    },
                    error: function(xhr) {
                        let message = 'Failed to add resource';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showAlert('error', message);
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalText);
                    }
                });
            }
            
            // Handle remove resource
            $(document).on('click', '.remove-resource', function() {
                const resourceId = $(this).data('resource-id');
                const resourceName = $(this).data('resource-name');
                
                if (confirm(`Are you sure you want to remove "${resourceName}" from this inquiry?`)) {
                    const removeBtn = $(this);
                    const originalHtml = removeBtn.html();
                    
                    // Show loading state
                    removeBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                    
                    $.ajax({
                        url: `/dashboard/inquiries/resources/${resourceId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                
                                // Remove row from table
                                $(`tr[data-resource-id="${resourceId}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                    
                                    // Show empty message if no resources left
                                    if ($('#resourcesTable tbody tr').length === 0) {
                                        $('#resourcesTable tbody').html(`
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    <i class="fas fa-info-circle me-2"></i>No resources added yet
                                                </td>
                                            </tr>
                                        `);
                                    }
                                });
                            } else {
                                showAlert('error', response.message || 'Failed to remove resource');
                                removeBtn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to remove resource';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('error', message);
                            removeBtn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });

            
            // Helper function to get resource type color
            function getResourceTypeColor(type) {
                switch(type) {
                    case 'hotel': return 'primary';
                    case 'vehicle': return 'success';
                    case 'guide': return 'info';
                    case 'representative': return 'warning';
                    case 'extra': return 'secondary';
                    default: return 'secondary';
                }
            }
            
            // Helper function to show alerts
            function showAlert(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Remove existing alerts
                $('.alert').remove();
                
                // Add new alert
                $('.card-body').prepend(alertHtml);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
        });
    } else {
        // jQuery not ready yet, try again in 100ms
        setTimeout(waitForJQuery, 100);
    }
}

// Start waiting for jQuery
waitForJQuery();
</script>
@endif
@endif