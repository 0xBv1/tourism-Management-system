@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Vehicle Calendar">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.vehicles.index') }}">Vehicles</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Vehicle Availability Calendar</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.vehicles.index') }}" class="btn btn-secondary btn-sm me-2">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                                @if(admin()->can('vehicles.create'))
                                    <a href="{{ route('dashboard.vehicles.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create Vehicle
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="vehicle_filter" class="form-label">Filter by Vehicle:</label>
                                    <select class="form-control" id="vehicle_filter">
                                        <option value="">All Vehicles</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="city_filter" class="form-label">Filter by City:</label>
                                    <select class="form-control" id="city_filter">
                                        <option value="">All Cities</option>
                                        @foreach($vehicles->pluck('city.name')->unique()->filter() as $cityName)
                                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            // Fetch events from API
            fetch(`/api/calendar/availability?resource_type=vehicle&start=${info.startStr}&end=${info.endStr}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    failureCallback(error);
                });
        },
        eventDidMount: function(info) {
            // Customize event appearance based on status
            if (info.event.extendedProps.status === 'occupied') {
                info.el.style.backgroundColor = '#dc3545';
            } else if (info.event.extendedProps.status === 'maintenance') {
                info.el.style.backgroundColor = '#ffc107';
            }
        }
    });
    
    calendar.render();
    
    // Filter functionality
    let currentFilters = {
        resource_id: '',
        city_id: ''
    };

    document.getElementById('vehicle_filter').addEventListener('change', function() {
        currentFilters.resource_id = this.value;
        calendar.refetchEvents();
    });
    
    document.getElementById('city_filter').addEventListener('change', function() {
        // Get city ID from the selected city name
        const cityName = this.value;
        const cityId = getCityIdByName(cityName);
        currentFilters.city_id = cityId;
        calendar.refetchEvents();
    });

    // Helper function to get city ID by name
    function getCityIdByName(cityName) {
        const cityOptions = @json($vehicles->pluck('city.id', 'city.name')->filter());
        return cityOptions[cityName] || '';
    }

    // Update the events function to use filters
    calendar.setOption('events', function(info, successCallback, failureCallback) {
        const params = new URLSearchParams({
            resource_type: 'vehicle',
            start: info.startStr,
            end: info.endStr,
            ...currentFilters
        });
        
        fetch(`/api/calendar/availability?${params}`)
            .then(response => response.json())
            .then(data => {
                successCallback(data);
            })
            .catch(error => {
                console.error('Error fetching calendar events:', error);
                failureCallback(error);
            });
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush
