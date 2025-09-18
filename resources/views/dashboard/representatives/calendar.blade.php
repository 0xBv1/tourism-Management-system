@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Representative Calendar">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.representatives.index') }}">Representatives</a></li>
            <li class="breadcrumb-item active">Calendar</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Representative Availability Calendar</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.representatives.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-list"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="representative_filter" class="form-label">Filter by Representative:</label>
                                    <select class="form-control" id="representative_filter">
                                        <option value="">All Representatives</option>
                                        @foreach($representatives as $representative)
                                            <option value="{{ $representative->id }}">{{ $representative->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="city_filter" class="form-label">Filter by City:</label>
                                    <select class="form-control" id="city_filter">
                                        <option value="">All Cities</option>
                                        @foreach($representatives->pluck('city.name')->unique()->filter() as $cityName)
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
            // This would typically fetch events from your API
            // For now, we'll show a placeholder
            successCallback([]);
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
    document.getElementById('representative_filter').addEventListener('change', function() {
        // Implement representative filtering logic
        console.log('Representative filter changed:', this.value);
    });
    
    document.getElementById('city_filter').addEventListener('change', function() {
        // Implement city filtering logic
        console.log('City filter changed:', this.value);
    });
});
</script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
@endpush
