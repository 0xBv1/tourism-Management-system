@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Assign Resources">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.bookings.index') }}">Bookings</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard.bookings.show', $bookingFile) }}">{{ $bookingFile->file_name }}</a></li>
            <li class="breadcrumb-item active">Assign Resources</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Assign Resources to {{ $bookingFile->file_name }}</h5>
                        </div>
                        <div class="card-body">
                            <form id="resource-assignment-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="resource_type" class="form-label">Resource Type <span class="text-danger">*</span></label>
                                            <select class="form-control" id="resource_type" name="resource_type" required>
                                                <option value="">Select Resource Type</option>
                                                <option value="hotel">Hotel</option>
                                                <option value="vehicle">Vehicle</option>
                                                <option value="guide">Guide</option>
                                                <option value="representative">Representative</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="resource_id" class="form-label">Resource <span class="text-danger">*</span></label>
                                            <select class="form-control" id="resource_id" name="resource_id" required disabled>
                                                <option value="">Select Resource Type First</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Start Time</label>
                                            <input type="time" class="form-control" id="start_time" name="start_time">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">End Time</label>
                                            <input type="time" class="form-control" id="end_time" name="end_time">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="unit_price" class="form-label">Unit Price</label>
                                            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" min="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="special_requirements" class="form-label">Special Requirements</label>
                                    <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" placeholder="Enter any special requirements..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional notes..."></textarea>
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('dashboard.bookings.show', $bookingFile) }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Assign Resource</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Available Resources Display -->
                    <div class="card mt-4" id="available-resources-card" style="display: none;">
                        <div class="card-header">
                            <h5>Available Resources</h5>
                        </div>
                        <div class="card-body">
                            <div id="available-resources-list">
                                <!-- Resources will be loaded here via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const resourceTypeSelect = document.getElementById('resource_type');
    const resourceIdSelect = document.getElementById('resource_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const availableResourcesCard = document.getElementById('available-resources-card');
    const availableResourcesList = document.getElementById('available-resources-list');
    const form = document.getElementById('resource-assignment-form');

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    endDateInput.min = today;

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });

    // Load resources when type changes
    resourceTypeSelect.addEventListener('change', function() {
        if (this.value) {
            loadResources(this.value);
            resourceIdSelect.disabled = false;
        } else {
            resourceIdSelect.disabled = true;
            resourceIdSelect.innerHTML = '<option value="">Select Resource Type First</option>';
            availableResourcesCard.style.display = 'none';
        }
    });

    // Load available resources when dates change
    [startDateInput, endDateInput].forEach(input => {
        input.addEventListener('change', function() {
            if (startDateInput.value && endDateInput.value && resourceTypeSelect.value) {
                loadAvailableResources();
            }
        });
    });

    function loadResources(resourceType) {
        fetch(`/dashboard/resources/${resourceType}`)
            .then(response => response.json())
            .then(data => {
                resourceIdSelect.innerHTML = '<option value="">Select Resource</option>';
                data.resources.forEach(resource => {
                    const option = document.createElement('option');
                    option.value = resource.id;
                    option.textContent = resource.name;
                    resourceIdSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading resources:', error);
            });
    }

    function loadAvailableResources() {
        const formData = new FormData();
        formData.append('resource_type', resourceTypeSelect.value);
        formData.append('start_date', startDateInput.value);
        formData.append('end_date', endDateInput.value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('/dashboard/resources/available', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.resources && data.resources.length > 0) {
                availableResourcesList.innerHTML = '';
                data.resources.forEach(resource => {
                    const resourceCard = createResourceCard(resource);
                    availableResourcesList.appendChild(resourceCard);
                });
                availableResourcesCard.style.display = 'block';
            } else {
                availableResourcesCard.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading available resources:', error);
        });
    }

    function createResourceCard(resource) {
        const card = document.createElement('div');
        card.className = 'card mb-3';
        card.innerHTML = `
            <div class="card-body">
                <h6 class="card-title">${resource.name}</h6>
                <p class="card-text">${resource.description}</p>
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Price: ${resource.currency} ${resource.price}</small>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">City: ${resource.city}</small>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="selectResource(${resource.id})">
                    Select This Resource
                </button>
            </div>
        `;
        return card;
    }

    // Make selectResource function global
    window.selectResource = function(resourceId) {
        resourceIdSelect.value = resourceId;
        // Scroll to form
        form.scrollIntoView({ behavior: 'smooth' });
    };

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        formData.append('booking_file_id', {{ $bookingFile->id }});

        fetch('{{ route("dashboard.resource-assignments.store", $bookingFile) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("dashboard.bookings.show", $bookingFile) }}';
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while assigning the resource');
        });
    });
});
</script>
@endpush




