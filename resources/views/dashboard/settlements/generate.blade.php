@extends('layouts.dashboard.app')

@section('title', 'Generate Settlements Automatically')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs mr-2"></i>
                        Generate Settlements Automatically
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('dashboard.settlements.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Back to Settlements
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <form action="{{ route('dashboard.settlements.generate-automatic') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="settlement_type" class="form-label">
                                                Settlement Type <span class="text-danger">*</span>
                                            </label>
                                            <select name="settlement_type" id="settlement_type" class="form-select @error('settlement_type') is-invalid @enderror" required>
                                                <option value="">Select Settlement Type</option>
                                                <option value="monthly" {{ old('settlement_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                                <option value="weekly" {{ old('settlement_type') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                                <option value="quarterly" {{ old('settlement_type') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                                <option value="yearly" {{ old('settlement_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                            </select>
                                            @error('settlement_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="year" class="form-label">
                                                Year <span class="text-danger">*</span>
                                            </label>
                                            <select name="year" id="year" class="form-select @error('year') is-invalid @enderror" required>
                                                @for($i = $currentYear - 2; $i <= $currentYear + 2; $i++)
                                                    <option value="{{ $i }}" {{ old('year', $currentYear) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            @error('year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group" id="month-group" style="display: none;">
                                            <label for="month" class="form-label">Month</label>
                                            <select name="month" id="month" class="form-select @error('month') is-invalid @enderror">
                                                <option value="">Select Month</option>
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ old('month', $currentMonth) == $i ? 'selected' : '' }}>
                                                        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                                    </option>
                                                @endfor
                                            </select>
                                            @error('month')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="resource_type" class="form-label">Resource Type (Optional)</label>
                                            <select name="resource_type" id="resource_type" class="form-select @error('resource_type') is-invalid @enderror">
                                                <option value="">All Resources</option>
                                                <option value="guide" {{ old('resource_type') == 'guide' ? 'selected' : '' }}>Guides</option>
                                                <option value="representative" {{ old('resource_type') == 'representative' ? 'selected' : '' }}>Representatives</option>
                                                <option value="hotel" {{ old('resource_type') == 'hotel' ? 'selected' : '' }}>Hotels</option>
                                                <option value="vehicle" {{ old('resource_type') == 'vehicle' ? 'selected' : '' }}>Vehicles</option>
                                                <option value="dahabia" {{ old('resource_type') == 'dahabia' ? 'selected' : '' }}>Dahabias</option>
                                                <option value="restaurant" {{ old('resource_type') == 'restaurant' ? 'selected' : '' }}>Restaurants</option>
                                                <option value="ticket" {{ old('resource_type') == 'ticket' ? 'selected' : '' }}>Tickets</option>
                                                <option value="extra" {{ old('resource_type') == 'extra' ? 'selected' : '' }}>Extra Services</option>
                                            </select>
                                            @error('resource_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group" id="specific-resource-group" style="display: none;">
                                            <label for="resource_id" class="form-label">Specific Resource (Optional)</label>
                                            <select name="resource_id" id="resource_id" class="form-select @error('resource_id') is-invalid @enderror">
                                                <option value="">All Resources of Selected Type</option>
                                            </select>
                                            @error('resource_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Leave empty to generate for all resources of the selected type, or select a specific resource.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" name="force" id="force" class="form-check-input" value="1" {{ old('force') ? 'checked' : '' }}>
                                                <label for="force" class="form-check-label">
                                                    Force regeneration (overwrite existing settlements)
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                Check this if you want to regenerate settlements even if they already exist for the selected period.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Settlement Settings -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mt-4 mb-3">
                                            <i class="fas fa-cog mr-2"></i>
                                            Settlement Settings
                                        </h5>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="commission_type" class="form-label">Commission Type</label>
                                            <select name="commission_type" id="commission_type" class="form-select @error('commission_type') is-invalid @enderror">
                                                <option value="percentage" {{ old('commission_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                                <option value="none" {{ old('commission_type') == 'none' ? 'selected' : '' }}>No Commission</option>
                                            </select>
                                            @error('commission_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="commission_value" class="form-label">Commission Value</label>
                                            <input type="number" name="commission_value" id="commission_value" 
                                                   class="form-control @error('commission_value') is-invalid @enderror" 
                                                   value="{{ old('commission_value', '10') }}" 
                                                   step="0.01" min="0">
                                            <small class="form-text text-muted">
                                                For percentage: enter percentage (e.g., 10 for 10%). For fixed: enter amount.
                                            </small>
                                            @error('commission_value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tax_rate" class="form-label">Tax Rate (%)</label>
                                            <input type="number" name="tax_rate" id="tax_rate" 
                                                   class="form-control @error('tax_rate') is-invalid @enderror" 
                                                   value="{{ old('tax_rate', '0') }}" 
                                                   step="0.01" min="0" max="100">
                                            @error('tax_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="deductions" class="form-label">Deductions</label>
                                            <input type="number" name="deductions" id="deductions" 
                                                   class="form-control @error('deductions') is-invalid @enderror" 
                                                   value="{{ old('deductions', '0') }}" 
                                                   step="0.01" min="0">
                                            @error('deductions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bonuses" class="form-label">Bonuses</label>
                                            <input type="number" name="bonuses" id="bonuses" 
                                                   class="form-control @error('bonuses') is-invalid @enderror" 
                                                   value="{{ old('bonuses', '0') }}" 
                                                   step="0.01" min="0">
                                            @error('bonuses')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-cogs mr-1"></i>
                                        Generate Settlements
                                    </button>
                                    <a href="{{ route('dashboard.settlements.index') }}" class="btn btn-secondary ml-2">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-dark">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h6>Settlement Types:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Monthly:</strong> Generate settlements for a specific month</li>
                                        <li><strong>Weekly:</strong> Generate weekly settlements</li>
                                        <li><strong>Quarterly:</strong> Generate quarterly settlements</li>
                                        <li><strong>Yearly:</strong> Generate yearly settlements</li>
                                    </ul>

                                    <h6 class="mt-3">Resource Types:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>All Resources:</strong> Generate settlements for all active resources</li>
                                        <li><strong>Specific Type:</strong> Generate settlements for all resources of a specific type</li>
                                        <li><strong>Specific Resource:</strong> Generate settlement for one specific resource only</li>
                                    </ul>

                                    <h6 class="mt-3">Settlement Settings:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Commission Type:</strong> Choose how commission is calculated</li>
                                        <li><strong>Commission Value:</strong> Set the commission amount or percentage</li>
                                        <li><strong>Tax Rate:</strong> Set the tax percentage to be applied</li>
                                        <li><strong>Deductions:</strong> Set any deductions to be applied</li>
                                        <li><strong>Bonuses:</strong> Set any bonuses to be added</li>
                                    </ul>

                                    <h6 class="mt-3">Settlement Status:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Default Status:</strong> All generated settlements will be created as "Pending"</li>
                                        <li><strong>Manual Review:</strong> You can review and calculate settlements manually</li>
                                        <li><strong>No Auto-Calculation:</strong> Settlements are not automatically calculated</li>
                                    </ul>

                                    <h6 class="mt-3">Force Regeneration:</h6>
                                    <ul class="list-unstyled">
                                        <li>Unchecked: Skip if settlement already exists</li>
                                        <li>Checked: Overwrite existing settlements</li>
                                    </ul>

                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <strong>Warning:</strong> This will create settlements based on your selection. For "All Resources", it will create settlements for all active resources. Use "Specific Resource" option to generate for one resource only.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settlementTypeSelect = document.getElementById('settlement_type');
    const monthGroup = document.getElementById('month-group');
    const resourceTypeSelect = document.getElementById('resource_type');
    const specificResourceGroup = document.getElementById('specific-resource-group');
    const resourceIdSelect = document.getElementById('resource_id');
    
    // Resource data from server
    const resources = {
        guide: @json($guides),
        representative: @json($representatives),
        hotel: @json($hotels),
        vehicle: @json($vehicles),
        dahabia: @json($dahabias),
        restaurant: @json($restaurants),
        ticket: @json($tickets),
        extra: @json($extras)
    };
    
    function toggleMonthField() {
        if (settlementTypeSelect.value === 'monthly') {
            monthGroup.style.display = 'block';
        } else {
            monthGroup.style.display = 'none';
        }
    }
    
    function toggleSpecificResourceField() {
        if (resourceTypeSelect.value) {
            specificResourceGroup.style.display = 'block';
            loadResourcesForType(resourceTypeSelect.value);
        } else {
            specificResourceGroup.style.display = 'none';
            resourceIdSelect.innerHTML = '<option value="">All Resources of Selected Type</option>';
        }
    }
    
    function loadResourcesForType(resourceType) {
        const resourceList = resources[resourceType] || [];
        resourceIdSelect.innerHTML = '<option value="">All Resources of Selected Type</option>';
        
        resourceList.forEach(function(resource) {
            const option = document.createElement('option');
            option.value = resource.id;
            option.textContent = resource.name || resource.title || `#${resource.id}`;
            resourceIdSelect.appendChild(option);
        });
    }
    
    settlementTypeSelect.addEventListener('change', toggleMonthField);
    resourceTypeSelect.addEventListener('change', toggleSpecificResourceField);
    
    toggleMonthField(); // Initial call
    toggleSpecificResourceField(); // Initial call
});
</script>
@endsection
