@extends('layouts.dashboard.app')

@section('title', 'Add New Settlement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add New Settlement</h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.settlements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <form action="{{ route('dashboard.settlements.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Resource Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resource_type">Resource Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('resource_type') is-invalid @enderror" 
                                            id="resource_type" name="resource_type" required>
                                        <option value="">Select Resource Type</option>
                                        <option value="guide" {{ old('resource_type') == 'guide' ? 'selected' : '' }}>Guide</option>
                                        <option value="representative" {{ old('resource_type') == 'representative' ? 'selected' : '' }}>Representative</option>
                                        <option value="hotel" {{ old('resource_type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                        <option value="vehicle" {{ old('resource_type') == 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                                        <option value="dahabia" {{ old('resource_type') == 'dahabia' ? 'selected' : '' }}>Dahabia</option>
                                        <option value="restaurant" {{ old('resource_type') == 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                                        <option value="ticket" {{ old('resource_type') == 'ticket' ? 'selected' : '' }}>Ticket</option>
                                        <option value="extra" {{ old('resource_type') == 'extra' ? 'selected' : '' }}>Extra Service</option>
                                    </select>
                                    @error('resource_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="resource_id">Resource <span class="text-danger">*</span></label>
                                    <select class="form-control @error('resource_id') is-invalid @enderror" 
                                            id="resource_id" name="resource_id" required>
                                        <option value="">Select Resource</option>
                                    </select>
                                    @error('resource_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Settlement Type -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="settlement_type">Settlement Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('settlement_type') is-invalid @enderror" 
                                            id="settlement_type" name="settlement_type" required>
                                        <option value="">Select Settlement Type</option>
                                        <option value="monthly" {{ old('settlement_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="weekly" {{ old('settlement_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                        <option value="quarterly" {{ old('settlement_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                        <option value="yearly" {{ old('settlement_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        <option value="custom" {{ old('settlement_type') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                    @error('settlement_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Year <span class="text-danger">*</span></label>
                                    <select class="form-control @error('year') is-invalid @enderror" 
                                            id="year" name="year" required>
                                        <option value="">Select Year</option>
                                        @for($year = date('Y'); $year >= 2020; $year--)
                                            <option value="{{ $year }}" {{ old('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endfor
                                    </select>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Month Selection (for monthly settlements) -->
                        <div class="row" id="month_selection" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="month">Month <span class="text-danger">*</span></label>
                                    <select class="form-control @error('month') is-invalid @enderror" 
                                            id="month" name="month">
                                        <option value="">Select Month</option>
                                        <option value="1" {{ old('month') == '1' ? 'selected' : '' }}>January</option>
                                        <option value="2" {{ old('month') == '2' ? 'selected' : '' }}>February</option>
                                        <option value="3" {{ old('month') == '3' ? 'selected' : '' }}>March</option>
                                        <option value="4" {{ old('month') == '4' ? 'selected' : '' }}>April</option>
                                        <option value="5" {{ old('month') == '5' ? 'selected' : '' }}>May</option>
                                        <option value="6" {{ old('month') == '6' ? 'selected' : '' }}>June</option>
                                        <option value="7" {{ old('month') == '7' ? 'selected' : '' }}>July</option>
                                        <option value="8" {{ old('month') == '8' ? 'selected' : '' }}>August</option>
                                        <option value="9" {{ old('month') == '9' ? 'selected' : '' }}>September</option>
                                        <option value="10" {{ old('month') == '10' ? 'selected' : '' }}>October</option>
                                        <option value="11" {{ old('month') == '11' ? 'selected' : '' }}>November</option>
                                        <option value="12" {{ old('month') == '12' ? 'selected' : '' }}>December</option>
                                    </select>
                                    @error('month')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Custom Date Range (for custom settlements) -->
                        <div class="row" id="custom_dates" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" value="{{ old('start_date') }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" value="{{ old('end_date') }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Commission Settings -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="commission_type">Commission Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('commission_type') is-invalid @enderror" 
                                            id="commission_type" name="commission_type" required>
                                        <option value="">Select Commission Type</option>
                                        <option value="percentage" {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                        <option value="none" {{ old('commission_type') == 'none' ? 'selected' : '' }}>No Commission</option>
                                    </select>
                                    @error('commission_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="commission_value">Commission Value</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('commission_value') is-invalid @enderror" 
                                           id="commission_value" name="commission_value" 
                                           value="{{ old('commission_value') }}" placeholder="0.00">
                                    @error('commission_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tax_rate">Tax Rate (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" 
                                           class="form-control @error('tax_rate') is-invalid @enderror" 
                                           id="tax_rate" name="tax_rate" 
                                           value="{{ old('tax_rate', 0) }}" placeholder="0.00">
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Additional Settings -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deductions">Deductions</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('deductions') is-invalid @enderror" 
                                           id="deductions" name="deductions" 
                                           value="{{ old('deductions', 0) }}" placeholder="0.00">
                                    @error('deductions')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bonuses">Bonuses</label>
                                    <input type="number" step="0.01" min="0" 
                                           class="form-control @error('bonuses') is-invalid @enderror" 
                                           id="bonuses" name="bonuses" 
                                           value="{{ old('bonuses', 0) }}" placeholder="0.00">
                                    @error('bonuses')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Available Bookings Preview -->
                        <div class="row mt-4" id="bookings_preview" style="display: none;">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <i class="fas fa-list"></i> Available Bookings for Settlement
                                        </h5>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="refresh_bookings">
                                                <i class="fas fa-sync"></i> Refresh
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="bookings_loading" class="text-center" style="display: none;">
                                            <i class="fas fa-spinner fa-spin"></i> Loading bookings...
                                        </div>
                                        <div id="bookings_content">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle"></i>
                                                Please select Resource Type, Resource, Settlement Type, and Year to view available bookings.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Settlement
                        </button>
                        <a href="{{ route('dashboard.settlements.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Load jQuery from CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    const guides = @json($guides ?? []);
    const representatives = @json($representatives ?? []);
    const hotels = @json($hotels ?? []);
    const vehicles = @json($vehicles ?? []);
    const dahabias = @json($dahabias ?? []);
    const restaurants = @json($restaurants ?? []);
    const tickets = @json($tickets ?? []);
    const extras = @json($extras ?? []);

    // Handle resource type change
    $('#resource_type').on('change', function() {
        const resourceType = $(this).val();
        const resourceSelect = $('#resource_id');
        
        // Clear existing options
        resourceSelect.empty();
        resourceSelect.append('<option value="">Select Resource</option>');
        
        let resourceData = [];
        
        switch(resourceType) {
            case 'guide':
                resourceData = guides;
                break;
            case 'representative':
                resourceData = representatives;
                break;
            case 'hotel':
                resourceData = hotels;
                break;
            case 'vehicle':
                resourceData = vehicles;
                break;
            case 'dahabia':
                resourceData = dahabias;
                break;
            case 'restaurant':
                resourceData = restaurants;
                break;
            case 'ticket':
                resourceData = tickets;
                break;
            case 'extra':
                resourceData = extras;
                break;
        }
        
        if (resourceData && resourceData.length > 0) {
            resourceData.forEach(function(resource) {
                let displayName = resource.name || 'Unnamed Resource';
                
                // Special handling for vehicles to show type
                if (resourceType === 'vehicle' && resource.type) {
                    displayName += ' (' + resource.type + ')';
                }
                
                resourceSelect.append('<option value="' + resource.id + '">' + displayName + '</option>');
            });
        } else {
            resourceSelect.append('<option value="">No resources available</option>');
        }
    });

    // Handle settlement type change
    $('#settlement_type').on('change', function() {
        const settlementType = $(this).val();
        
        // Hide all conditional fields
        $('#month_selection').hide();
        $('#custom_dates').hide();
        
        // Show relevant fields based on settlement type
        if (settlementType === 'monthly') {
            $('#month_selection').show();
        } else if (settlementType === 'custom') {
            $('#custom_dates').show();
        }
    });

    // Handle commission type change
    $('#commission_type').on('change', function() {
        const commissionType = $(this).val();
        const commissionValueField = $('#commission_value');
        
        if (commissionType === 'none') {
            commissionValueField.prop('disabled', true).val('0');
        } else {
            commissionValueField.prop('disabled', false);
        }
    });

    // Load bookings when required fields are selected
    function loadBookings() {
        const resourceType = $('#resource_type').val();
        const resourceId = $('#resource_id').val();
        const settlementType = $('#settlement_type').val();
        const year = $('#year').val();
        const month = $('#month').val();
        
        // Show bookings preview if all required fields are selected
        if (resourceType && resourceId && settlementType && year) {
            $('#bookings_preview').show();
            
            // Show loading
            $('#bookings_loading').show();
            $('#bookings_content').html('');
            
            // Prepare date range based on settlement type
            let startDate, endDate;
            
            if (settlementType === 'monthly' && month) {
                startDate = year + '-' + month.padStart(2, '0') + '-01';
                const lastDay = new Date(year, month, 0).getDate();
                endDate = year + '-' + month.padStart(2, '0') + '-' + lastDay;
            } else if (settlementType === 'yearly') {
                startDate = year + '-01-01';
                endDate = year + '-12-31';
            } else if (settlementType === 'quarterly') {
                // For quarterly, we'll use the full year for now
                startDate = year + '-01-01';
                endDate = year + '-12-31';
            } else if (settlementType === 'weekly') {
                // For weekly, we'll use the full year for now
                startDate = year + '-01-01';
                endDate = year + '-12-31';
            } else {
                // For custom, we'll use the full year for now
                startDate = year + '-01-01';
                endDate = year + '-12-31';
            }
            
            // Make AJAX request to get bookings
            $.ajax({
                url: '/dashboard/settlements/get-resource-bookings',
                method: 'GET',
                data: {
                    resource_type: resourceType,
                    resource_id: resourceId,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    $('#bookings_loading').hide();
                    
                    if (response.success && response.bookings && response.bookings.length > 0) {
                        let html = '<div class="table-responsive"><table class="table table-bordered table-striped">';
                        html += '<thead><tr>';
                        html += '<th>Booking Date</th>';
                        html += '<th>Client Name</th>';
                        html += '<th>Tour Name</th>';
                        html += '<th>Duration</th>';
                        html += '<th>Unit Price</th>';
                        html += '<th>Total Amount</th>';
                        html += '<th>Status</th>';
                        html += '</tr></thead><tbody>';
                        
                        let totalAmount = 0;
                        
                        response.bookings.forEach(function(booking) {
                            html += '<tr>';
                            html += '<td>' + booking.booking_date + '</td>';
                            html += '<td>' + (booking.client_name || 'N/A') + '</td>';
                            html += '<td>' + (booking.tour_name || 'N/A') + '</td>';
                            html += '<td>' + (booking.duration_text || 'N/A') + '</td>';
                            html += '<td>' + (booking.formatted_unit_price || 'N/A') + '</td>';
                            html += '<td>' + (booking.formatted_amount || 'N/A') + '</td>';
                            html += '<td><span class="badge badge-' + (booking.status_color || 'secondary') + '">' + (booking.status_label || 'N/A') + '</span></td>';
                            html += '</tr>';
                            
                            totalAmount += parseFloat(booking.amount || 0);
                        });
                        
                        html += '</tbody><tfoot>';
                        html += '<tr class="table-primary">';
                        html += '<th colspan="5">Total Amount</th>';
                        html += '<th>' + response.formatted_total_amount + '</th>';
                        html += '<th></th>';
                        html += '</tr>';
                        html += '</tfoot></table></div>';
                        
                        html += '<div class="alert alert-success mt-3">';
                        html += '<strong>Summary:</strong> Found ' + response.bookings.length + ' bookings with total amount of ' + response.formatted_total_amount;
                        html += '</div>';
                        
                        $('#bookings_content').html(html);
                    } else {
                        $('#bookings_content').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> No bookings found for the selected criteria.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#bookings_loading').hide();
                    $('#bookings_content').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error loading bookings: ' + error + '</div>');
                }
            });
        } else {
            $('#bookings_preview').hide();
        }
    }
    
    // Add event listeners for form changes
    $('#resource_type, #resource_id, #settlement_type, #year, #month').on('change', function() {
        loadBookings();
    });
    
    // Refresh bookings button
    $('#refresh_bookings').on('click', function() {
        loadBookings();
    });

    // Initialize form based on old values
    if ($('#resource_type').val()) {
        $('#resource_type').trigger('change');
        if ($('#resource_id').val()) {
            $('#resource_id').val('{{ old("resource_id") }}');
        }
    }
    
    if ($('#settlement_type').val()) {
        $('#settlement_type').trigger('change');
    }
    
    if ($('#commission_type').val()) {
        $('#commission_type').trigger('change');
    }
    
    // Load bookings if form has values
    setTimeout(function() {
        loadBookings();
    }, 500);
});

// Fallback using vanilla JavaScript if jQuery fails
if (typeof $ === 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        const resourceTypeSelect = document.getElementById('resource_type');
        const resourceIdSelect = document.getElementById('resource_id');
        
        if (resourceTypeSelect && resourceIdSelect) {
            resourceTypeSelect.addEventListener('change', function() {
                const resourceType = this.value;
                
                // Clear existing options
                resourceIdSelect.innerHTML = '<option value="">Select Resource</option>';
                
                // Get resource data
                const guides = @json($guides ?? []);
                const representatives = @json($representatives ?? []);
                const hotels = @json($hotels ?? []);
                const vehicles = @json($vehicles ?? []);
                const dahabias = @json($dahabias ?? []);
                const restaurants = @json($restaurants ?? []);
                const tickets = @json($tickets ?? []);
                const extras = @json($extras ?? []);
                
                let resourceData = [];
                
                switch(resourceType) {
                    case 'guide':
                        resourceData = guides;
                        break;
                    case 'representative':
                        resourceData = representatives;
                        break;
                    case 'hotel':
                        resourceData = hotels;
                        break;
                    case 'vehicle':
                        resourceData = vehicles;
                        break;
                    case 'dahabia':
                        resourceData = dahabias;
                        break;
                    case 'restaurant':
                        resourceData = restaurants;
                        break;
                    case 'ticket':
                        resourceData = tickets;
                        break;
                    case 'extra':
                        resourceData = extras;
                        break;
                }
                
                if (resourceData && resourceData.length > 0) {
                    resourceData.forEach(function(resource) {
                        const option = document.createElement('option');
                        option.value = resource.id;
                        option.textContent = resource.name || 'Unnamed Resource';
                        resourceIdSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No resources available';
                    resourceIdSelect.appendChild(option);
                }
            });
        }
    });
}
</script>
