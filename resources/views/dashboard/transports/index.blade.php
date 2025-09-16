@extends('layouts.dashboard.app')

@section('content')
    <!-- Container-fluid starts-->
    <x-dashboard.partials.breadcrumb title="Transports" :hideFirst="true">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard.transports.index') }}">Transports</a>
        </li>
    </x-dashboard.partials.breadcrumb>
    <!-- Container-fluid Ends-->

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <x-dashboard.partials.message-alert/>

            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Transports</h5>
                            <a href="{{ route('dashboard.transports.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Create Transport
                            </a>
                        </div>
                        
                        <!-- Filters -->
                        <div class="row mt-3">
                            <div class="col-md-2">
                                <select id="transport_type_filter" class="form-select form-select-sm">
                                    <option value="">All Transport Types</option>
                                    <option value="bus">Bus</option>
                                    <option value="train">Train</option>
                                    <option value="ferry">Ferry</option>
                                    <option value="plane">Plane</option>
                                    <option value="car">Car</option>
                                    <option value="van">Van</option>
                                    <option value="boat">Boat</option>
                                    <option value="helicopter">Helicopter</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="vehicle_type_filter" class="form-select form-select-sm">
                                    <option value="">All Vehicle Types</option>
                                    <option value="sedan">Sedan</option>
                                    <option value="suv">SUV</option>
                                    <option value="van">Van</option>
                                    <option value="bus">Bus</option>
                                    <option value="train">Train</option>
                                    <option value="boat">Boat</option>
                                    <option value="plane">Plane</option>
                                    <option value="helicopter">Helicopter</option>
                                    <option value="limousine">Limousine</option>
                                    <option value="motorcycle">Motorcycle</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="route_type_filter" class="form-select form-select-sm">
                                    <option value="">All Route Types</option>
                                    <option value="direct">Direct</option>
                                    <option value="with_stops">With Stops</option>
                                    <option value="circular">Circular</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="status_filter" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button id="clear_filters" class="btn btn-sm btn-outline-secondary">
                                    <i class="mdi mdi-filter-remove"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
    
    <script>
        $(document).ready(function() {
            // Filter functionality
            $('#transport_type_filter, #vehicle_type_filter, #route_type_filter, #status_filter').on('change', function() {
                var transportType = $('#transport_type_filter').val();
                var vehicleType = $('#vehicle_type_filter').val();
                var routeType = $('#route_type_filter').val();
                var status = $('#status_filter').val();
                
                // Update URL parameters
                var url = new URL(window.location);
                if (transportType) url.searchParams.set('transport_type', transportType);
                else url.searchParams.delete('transport_type');
                
                if (vehicleType) url.searchParams.set('vehicle_type', vehicleType);
                else url.searchParams.delete('vehicle_type');
                
                if (routeType) url.searchParams.set('route_type', routeType);
                else url.searchParams.delete('route_type');
                
                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');
                
                // Reload the page with new parameters
                window.location.href = url.toString();
            });
            
            // Clear filters
            $('#clear_filters').on('click', function() {
                window.location.href = '{{ route("dashboard.transports.index") }}';
            });
            
            // Set initial filter values from URL parameters
            var urlParams = new URLSearchParams(window.location.search);
            $('#transport_type_filter').val(urlParams.get('transport_type') || '');
            $('#vehicle_type_filter').val(urlParams.get('vehicle_type') || '');
            $('#route_type_filter').val(urlParams.get('route_type') || '');
            $('#status_filter').val(urlParams.get('status') || '');
        });
    </script>
@endpush
