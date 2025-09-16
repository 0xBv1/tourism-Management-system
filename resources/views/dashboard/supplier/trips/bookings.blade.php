@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Trip Bookings">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.trips.index') }}">Trips</a></li>
            <li class="breadcrumb-item active">Bookings</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Bookings for Trip: {{ $trip->departure_city }} → {{ $trip->arrival_city }} ({{ $trip->travel_date->format('M d, Y') }})</h5>
                            <div class="card-header-right">
                                <a href="{{ route('dashboard.trips.show', $trip) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-eye"></i> View Trip
                                </a>
                                <a href="{{ route('dashboard.trips.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
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
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush 