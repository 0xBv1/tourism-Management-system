@extends('layouts.dashboard.app')

@push('css')
    <style>
        .filter-card {
            border-left: 4px solid #007bff;
        }
        .filter-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .form-label {
            color: #495057;
            font-size: 0.9rem;
        }
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .filter-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .results-counter {
            background-color: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Service Approvals">
            <li class="breadcrumb-item active">Service Approvals</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-xl-3 col-md-6">
                            <div class="card o-hidden">
                                <div class="card-body">
                                    <div class="d-flex static-top-widget">
                                        <div class="align-self-center">
                                            <div class="icon-bg">
                                                <i class="fa fa-list"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-roboto">Total Approvals</span>
                                            <h4 class="font-roboto">{{ $stats['total'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card o-hidden">
                                <div class="card-body">
                                    <div class="d-flex static-top-widget">
                                        <div class="align-self-center">
                                            <div class="icon-bg bg-warning">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-roboto">Pending</span>
                                            <h4 class="font-roboto">{{ $stats['pending'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card o-hidden">
                                <div class="card-body">
                                    <div class="d-flex static-top-widget">
                                        <div class="align-self-center">
                                            <div class="icon-bg bg-success">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-roboto">Approved</span>
                                            <h4 class="font-roboto">{{ $stats['approved'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card o-hidden">
                                <div class="card-body">
                                    <div class="d-flex static-top-widget">
                                        <div class="align-self-center">
                                            <div class="icon-bg bg-danger">
                                                <i class="fa fa-times"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="font-roboto">Rejected</span>
                                            <h4 class="font-roboto">{{ $stats['rejected'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4 filter-card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fa fa-filter me-2"></i>Filters
                                </h5>
                                @if(request('status') || request('service_type') || request('supplier_id'))
                                    <a href="{{ route('dashboard.service-approvals.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times me-1"></i>Clear All
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="filterForm" method="GET" action="{{ route('dashboard.service-approvals.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label for="status" class="form-label fw-bold">
                                        <i class="fa fa-info-circle me-1"></i>Status
                                    </label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            <i class="fa fa-clock-o"></i> Pending
                                        </option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                            <i class="fa fa-check"></i> Approved
                                        </option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            <i class="fa fa-times"></i> Rejected
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="service_type" class="form-label fw-bold">
                                        <i class="fa fa-cog me-1"></i>Service Type
                                    </label>
                                    <select name="service_type" id="service_type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="hotel" {{ request('service_type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                        <option value="tour" {{ request('service_type') == 'tour' ? 'selected' : '' }}>Tour</option>
                                        <option value="trip" {{ request('service_type') == 'trip' ? 'selected' : '' }}>Trip</option>
                                        <option value="transport" {{ request('service_type') == 'transport' ? 'selected' : '' }}>Transport</option>
                                        <option value="room" {{ request('service_type') == 'room' ? 'selected' : '' }}>Room</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="supplier_id" class="form-label fw-bold">
                                        <i class="fa fa-building me-1"></i>Supplier
                                    </label>
                                    <select name="supplier_id" id="supplier_id" class="form-select">
                                        <option value="">All Suppliers</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fa fa-search me-1"></i>Apply Filters
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-clear">
                                        <i class="fa fa-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Approvals Table -->
                    <div class="card">
                        <div class="card-header">
                                                            <div class="d-flex justify-content-between align-items-center">
                                    <h5>Service Approvals</h5>
                                    <div class="d-flex align-items-center">
                                        @if(request('status') || request('service_type') || request('supplier_id'))
                                            <div class="text-muted small me-3">
                                                <i class="fa fa-filter"></i> Filters applied
                                                                                        @if(request('status'))
                                            <span class="badge bg-info ms-1 filter-badge">{{ ucfirst(request('status')) }}</span>
                                        @endif
                                        @if(request('service_type'))
                                            <span class="badge bg-info ms-1 filter-badge">{{ ucfirst(request('service_type')) }}</span>
                                        @endif
                                        @if(request('supplier_id'))
                                            <span class="badge bg-info ms-1 filter-badge">Supplier #{{ request('supplier_id') }}</span>
                                        @endif
                                            </div>
                                        @endif
                                        <div class="text-muted small results-counter">
                                            <i class="fa fa-list"></i> Showing {{ $stats['total'] }} result(s)
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="card-body order-datatable overflow-x-auto">
                            <div class="">
                                {!! $dataTable->table(['class'=>'display'], true) !!}
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
    {!! $dataTable->scripts() !!}
    
    <script>
        $(document).ready(function() {
            // Handle filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                var submitBtn = $(this).find('button[type="submit"]');
                var originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin me-1"></i>Applying...');
                submitBtn.prop('disabled', true);
                
                // Get form data
                var formData = $(this).serialize();
                
                // Update DataTable with new filters
                $('#service-approvals-table').DataTable().ajax.reload(function() {
                    // Re-enable button after reload
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);
                    
                    // Show success message
                    if (formData) {
                        toastr.success('Filters applied successfully!');
                    }
                });
                
                // Update URL with filters
                var newUrl = '{{ route('dashboard.service-approvals.index') }}?' + formData;
                window.history.pushState({}, '', newUrl);
            });
            
            // Handle clear filters
            $('.btn-clear').on('click', function(e) {
                e.preventDefault();
                
                // Show loading state
                var clearBtn = $(this);
                var originalText = clearBtn.html();
                clearBtn.html('<i class="fa fa-spinner fa-spin me-1"></i>Clearing...');
                clearBtn.prop('disabled', true);
                
                // Clear form
                $('#filterForm')[0].reset();
                
                // Reload DataTable
                $('#service-approvals-table').DataTable().ajax.reload(function() {
                    // Re-enable button after reload
                    clearBtn.html(originalText);
                    clearBtn.prop('disabled', false);
                    
                    // Show success message
                    toastr.success('Filters cleared successfully!');
                });
                
                // Update URL
                window.history.pushState({}, '', '{{ route('dashboard.service-approvals.index') }}');
            });
            
            // Auto-submit form when select values change (optional)
            $('#status, #service_type, #supplier_id').on('change', function() {
                // Uncomment the line below if you want auto-submit on change
                // $('#filterForm').submit();
            });
        });
    </script>
@endpush
