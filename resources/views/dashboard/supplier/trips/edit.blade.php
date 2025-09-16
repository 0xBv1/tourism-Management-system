@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('supplier.trips.update', $trip) }}" method="POST" class="page-body" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Trip" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('supplier.trips.index') }}">Trips</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['trip_details', 'schedule', 'amenities']"
                            tab-id="trip-tabs">
                            
                            <!-- Trip Details Tab -->
                            <div class="tab-pane fade active show"
                                 id="{{ 'trip-tabs-0' }}" role="tabpanel"
                                 aria-labelledby="{{ 'trip-tabs-0' }}-tab">

                                <x-dashboard.form.input-select 
                                    id="trip_type"
                                    name="trip_type"
                                    labelTitle="Trip Type"
                                    :options="$tripTypes"
                                    errorKey="trip_type"
                                    required="true"
                                    :value="old('trip_type', $trip->trip_type)"
                                />

                                <x-dashboard.form.input-select 
                                    id="departure_city"
                                    name="departure_city"
                                    labelTitle="Departure City"
                                    :options="$cities"
                                    errorKey="departure_city"
                                    required="true"
                                    :value="old('departure_city', $trip->departure_city)"
                                />

                                <x-dashboard.form.input-select 
                                    id="arrival_city"
                                    name="arrival_city"
                                    labelTitle="Arrival City"
                                    :options="$cities"
                                    errorKey="arrival_city"
                                    required="true"
                                    :value="old('arrival_city', $trip->arrival_city)"
                                />

                                <x-dashboard.form.input-number 
                                    id="seat_price"
                                    name="seat_price"
                                    labelTitle="Seat Price (EGP)"
                                    value="{{ old('seat_price', $trip->seat_price) }}"
                                    errorKey="seat_price"
                                />

                                <x-dashboard.form.input-number 
                                    id="total_seats"
                                    name="total_seats"
                                    labelTitle="Total Seats"
                                    value="{{ old('total_seats', $trip->total_seats) }}"
                                    errorKey="total_seats"
                                />

                                <x-dashboard.form.input-checkbox 
                                    id="enabled"
                                    name="enabled"
                                    labelTitle="Enabled"
                                    :value="true"
                                    resourceName="trip"
                                    resourceDesc="Enable"
                                    errorKey="enabled"
                                />

                            </div>

                            <!-- Schedule Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'trip-tabs-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'trip-tabs-1' }}-tab">

                                <x-dashboard.form.input-date 
                                    id="travel_date"
                                    name="travel_date"
                                    labelTitle="Travel Date"
                                    value="{{ old('travel_date', $trip->travel_date->format('Y-m-d')) }}"
                                    errorKey="travel_date"
                                    required="true"
                                />

                                <x-dashboard.form.input-date 
                                    id="return_date"
                                    name="return_date"
                                    labelTitle="Return Date"
                                    value="{{ old('return_date', $trip->return_date ? $trip->return_date->format('Y-m-d') : '') }}"
                                    errorKey="return_date"
                                />

                                <x-dashboard.form.input-time 
                                    id="departure_time"
                                    name="departure_time"
                                    labelTitle="Departure Time"
                                    value="{{ old('departure_time', $trip->departure_time ? $trip->departure_time->format('H:i') : '') }}"
                                    errorKey="departure_time"
                                    required="true"
                                />

                                <x-dashboard.form.input-time 
                                    id="arrival_time"
                                    name="arrival_time"
                                    labelTitle="Arrival Time"
                                    value="{{ old('arrival_time', $trip->arrival_time ? $trip->arrival_time->format('H:i') : '') }}"
                                    errorKey="arrival_time"
                                    required="true"
                                />

                            </div>

                            <!-- Amenities Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'trip-tabs-2' }}" role="tabpanel"
                                 aria-labelledby="{{ 'trip-tabs-2' }}-tab">

                                <x-dashboard.form.input-select 
                                    id="amenities"
                                    name="amenities[]"
                                    labelTitle="Amenities"
                                    :options="$amenities"
                                    track-by="id"
                                    option-lable="name"
                                    errorKey="amenities"
                                    multible="true"
                                    :value="old('amenities', $trip->amenities ?? [])"
                                />

                                <x-dashboard.form.input-textarea 
                                    id="additional_notes"
                                    name="additional_notes"
                                    labelTitle="Additional Notes"
                                    value="{{ old('additional_notes', $trip->additional_notes) }}"
                                    errorKey="additional_notes"
                                    rows="4"
                                />

                            </div>

                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button 
                            title="Update Trip"
                            class="btn-primary"
                        />

                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Time input enhancements
            const departureTimeInput = document.getElementById('departure_time');
            const arrivalTimeInput = document.getElementById('arrival_time');
            const returnDateInput = document.getElementById('return_date');
            const travelDateInput = document.getElementById('travel_date');

            // Set min/max times for better UX
            if (departureTimeInput) {
                departureTimeInput.min = '00:00';
                departureTimeInput.max = '23:59';
            }
            if (arrivalTimeInput) {
                arrivalTimeInput.min = '00:00';
                arrivalTimeInput.max = '23:59';
            }

            // Auto-validate arrival time is after departure time
            function validateTimes() {
                const departureTime = departureTimeInput?.value;
                const arrivalTime = arrivalTimeInput?.value;
                
                if (departureTime && arrivalTime && departureTime >= arrivalTime) {
                    arrivalTimeInput.setCustomValidity('Arrival time must be after departure time');
                } else {
                    arrivalTimeInput.setCustomValidity('');
                }
            }

            // Validate return date is after travel date (only if return date is provided)
            function validateReturnDate() {
                const travelDate = travelDateInput?.value;
                const returnDate = returnDateInput?.value;
                
                if (travelDate && returnDate && returnDate <= travelDate) {
                    returnDateInput.setCustomValidity('Return date must be after travel date');
                } else {
                    returnDateInput.setCustomValidity('');
                }
            }

            if (departureTimeInput) {
                departureTimeInput.addEventListener('change', validateTimes);
            }
            if (arrivalTimeInput) {
                arrivalTimeInput.addEventListener('change', validateTimes);
            }
            if (travelDateInput) {
                travelDateInput.addEventListener('change', validateReturnDate);
            }
            if (returnDateInput) {
                returnDateInput.addEventListener('change', validateReturnDate);
            }
        });
    </script>
@endsection
 