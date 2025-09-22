@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Guide Calendar">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.guides.index') }}">Guides</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Guide Availability Calendar</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.guides.index') }}" class="btn btn-secondary btn-sm me-2">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                                @if(admin()->can('guides.create'))
                                    <a href="{{ route('dashboard.guides.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Create Guide
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="guide_filter" class="form-label">Filter by Guide:</label>
                                    <select class="form-control" id="guide_filter">
                                        <option value="">All Guides</option>
                                        @foreach($guides as $guide)
                                            <option value="{{ $guide->id }}">{{ $guide->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="city_filter" class="form-label">Filter by City:</label>
                                    <select class="form-control" id="city_filter">
                                        <option value="">All Cities</option>
                                        @foreach($guides->pluck('city.name')->unique()->filter() as $cityName)
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
    console.log('DOM loaded, initializing calendar...');
    
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar library not loaded!');
        return;
    }
    
    console.log('FullCalendar library loaded:', FullCalendar);
    
    var calendarEl = document.getElementById('calendar');
    console.log('Calendar element:', calendarEl);
    
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(info, successCallback, failureCallback) {
            console.log('Fetching calendar events for:', info.startStr, 'to', info.endStr);
            
            // Add a test event first
            const testEvents = [{
                id: 'test-event',
                title: 'Test Event',
                start: new Date().toISOString().split('T')[0],
                color: '#ff0000'
            }];
            
            // Fetch events from API
            fetch(`/api/calendar/availability?resource_type=guide&start=${info.startStr}&end=${info.endStr}`)
                .then(response => {
                    console.log('API Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Calendar events data:', data);
                    // Combine test events with API data
                    const allEvents = [...testEvents, ...data];
                    successCallback(allEvents);
                })
                .catch(error => {
                    console.error('Error fetching calendar events:', error);
                    // Return test events even if API fails
                    successCallback(testEvents);
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
    
    console.log('Calendar created, rendering...');
    calendar.render();
    console.log('Calendar rendered successfully');
    
    // Filter functionality
    let currentFilters = {
        resource_id: '',
        city_id: ''
    };

    document.getElementById('guide_filter').addEventListener('change', function() {
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
        const cityOptions = @json($guides->pluck('city.id', 'city.name')->filter());
        return cityOptions[cityName] || '';
    }

    // Update the events function to use filters
    calendar.setOption('events', function(info, successCallback, failureCallback) {
        const params = new URLSearchParams({
            resource_type: 'guide',
            start: info.startStr,
            end: info.endStr,
            ...currentFilters
        });
        
        console.log('Fetching filtered calendar events with params:', params.toString());
        
        fetch(`/api/calendar/availability?${params}`)
            .then(response => {
                console.log('Filtered API Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Filtered calendar events data:', data);
                successCallback(data);
            })
            .catch(error => {
                console.error('Error fetching filtered calendar events:', error);
                failureCallback(error);
            });
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
<style>
#calendar {
    height: 600px;
    min-height: 600px;
}
</style>
@endpush
