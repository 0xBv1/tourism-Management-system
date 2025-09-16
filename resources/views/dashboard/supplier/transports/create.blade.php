@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('supplier.transports.store') }}" method="POST" class="page-body" enctype="multipart/form-data">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Transport" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('supplier.transports.index') }}">Transports</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="transports">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'transports-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'transports-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.name"
                                                                 name="{{$localKey}}[name]" id="{{$localKey}}-name"
                                                                 :value="old($localKey.'.name')"
                                                                 label-title="Name"/>

                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.description"
                                                                 name="{{$localKey}}[description]"
                                                                 id="{{$localKey}}-description"
                                                                 :value="old($localKey.'.description')"
                                                                 label-title="Description"/>
                                </div>
                            @endforeach
                        </x-dashboard.form.language-multi-tab-card>
                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <div class="card tab2-card">
                    <div class="card-body">
                        <x-dashboard.form.multi-tab-card
                            :tabs="['basic', 'route', 'pricing', 'schedule', 'contact', 'media', 'seo']"
                            tab-id="transport-details">
                            
                            <!-- Basic Information Tab -->
                            <div class="tab-pane fade active show"
                                 id="{{ 'transport-details-0' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-0' }}-tab">

                                <x-dashboard.form.input-text error-key="slug"
                                                             name="slug" id="slug"
                                                             :value="old('slug')"
                                                             label-title="Slug"
                                                             placeholder="Leave empty for automatic generation from name"/>

                                <x-dashboard.form.input-select
                                    name="transport_type"
                                    :value="old('transport_type')"
                                    :options="$transportTypes"
                                    label-title="Transport Type"
                                    id="transport_type"
                                    error-key="transport_type"/>

                                <x-dashboard.form.input-select
                                    name="vehicle_type"
                                    :value="old('vehicle_type')"
                                    :options="$vehicleTypes"
                                    label-title="Vehicle Type"
                                    id="vehicle_type"
                                    error-key="vehicle_type"/>

                                <x-dashboard.form.input-text error-key="seating_capacity" 
                                                             name="seating_capacity" 
                                                             id="seating_capacity" 
                                                             :value="old('seating_capacity')" 
                                                             label-title="Seating Capacity"
                                                             type="number"/>

                                <x-dashboard.form.input-text error-key="vehicle_registration" 
                                                             name="vehicle_registration" 
                                                             id="vehicle_registration" 
                                                             :value="old('vehicle_registration')" 
                                                             label-title="Vehicle Registration"/>

                                <x-dashboard.form.input-select
                                    name="amenities[]"
                                    multible
                                    :value="old('amenities', [])"
                                    :options="$amenities"
                                    track-by="id"
                                    option-lable="name"
                                    label-title="Amenities"
                                    id="amenities"
                                    error-key="amenities"/>

                                <x-dashboard.form.input-checkbox resource-name="Transport" :value="true"
                                                                 error-key="enabled"
                                                                 name="enabled" id="enabled"
                                                                 label-title="Enabled"/>
                            </div>

                            <!-- Route Information Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-1' }}-tab">

                                <x-dashboard.form.input-text error-key="origin_location" 
                                                             name="origin_location" 
                                                             id="origin_location" 
                                                             :value="old('origin_location')" 
                                                             label-title="Origin Location"/>

                                <x-dashboard.form.input-text error-key="destination_location" 
                                                             name="destination_location" 
                                                             id="destination_location" 
                                                             :value="old('destination_location')" 
                                                             label-title="Destination Location"/>

                                <x-dashboard.form.input-text error-key="intermediate_stops" 
                                                             name="intermediate_stops" 
                                                             id="intermediate_stops" 
                                                             :value="old('intermediate_stops')" 
                                                             label-title="Intermediate Stops"
                                                             placeholder="Comma separated list of stops"/>

                                <x-dashboard.form.input-select
                                    name="route_type"
                                    :value="old('route_type')"
                                    :options="$routeTypes"
                                    label-title="Route Type"
                                    id="route_type"
                                    error-key="route_type"/>

                                <x-dashboard.form.input-text error-key="estimated_travel_time" 
                                                             name="estimated_travel_time" 
                                                             id="estimated_travel_time" 
                                                             :value="old('estimated_travel_time')" 
                                                             label-title="Estimated Travel Time (minutes)"
                                                             type="number"/>

                                <x-dashboard.form.input-text error-key="distance" 
                                                             name="distance" 
                                                             id="distance" 
                                                             :value="old('distance')" 
                                                             label-title="Distance (km)"
                                                             type="number"
                                                             step="0.01"/>
                            </div>

                            <!-- Pricing Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-2' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-2' }}-tab">

                                <x-dashboard.form.input-text error-key="price" 
                                                             name="price" 
                                                             id="price" 
                                                             :value="old('price')" 
                                                             label-title="Base Price"
                                                             type="number"
                                                             step="0.01"/>

                                <x-dashboard.form.input-select
                                    name="currency"
                                    :value="old('currency')"
                                    :options="$currencies"
                                    label-title="Currency"
                                    id="currency"
                                    error-key="currency"/>

                                <x-dashboard.form.input-text error-key="price_per_hour" 
                                                             name="price_per_hour" 
                                                             id="price_per_hour" 
                                                             :value="old('price_per_hour')" 
                                                             label-title="Price Per Hour"
                                                             type="number"
                                                             step="0.01"/>

                                <x-dashboard.form.input-text error-key="price_per_day" 
                                                             name="price_per_day" 
                                                             id="price_per_day" 
                                                             :value="old('price_per_day')" 
                                                             label-title="Price Per Day"
                                                             type="number"
                                                             step="0.01"/>

                                <x-dashboard.form.input-text error-key="price_per_km" 
                                                             name="price_per_km" 
                                                             id="price_per_km" 
                                                             :value="old('price_per_km')" 
                                                             label-title="Price Per KM"
                                                             type="number"
                                                             step="0.01"/>

                                <x-dashboard.form.input-text error-key="discount_percentage" 
                                                             name="discount_percentage" 
                                                             id="discount_percentage" 
                                                             :value="old('discount_percentage')" 
                                                             label-title="Discount Percentage"
                                                             type="number"
                                                             step="0.01"/>

                                <x-dashboard.form.input-text error-key="discount_conditions" 
                                                             name="discount_conditions" 
                                                             id="discount_conditions" 
                                                             :value="old('discount_conditions')" 
                                                             label-title="Discount Conditions"/>

                                <x-dashboard.form.input-text error-key="pricing_notes" 
                                                             name="pricing_notes" 
                                                             id="pricing_notes" 
                                                             :value="old('pricing_notes')" 
                                                             label-title="Pricing Notes"/>
                            </div>

                            <!-- Schedule Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-3' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-3' }}-tab">

                                <x-dashboard.form.input-time error-key="departure_time" 
                                                           name="departure_time" 
                                                           id="departure_time" 
                                                           :value="old('departure_time')" 
                                                           label-title="Departure Time"/>

                                <x-dashboard.form.input-time error-key="arrival_time" 
                                                           name="arrival_time" 
                                                           id="arrival_time" 
                                                           :value="old('arrival_time')" 
                                                           label-title="Arrival Time"/>

                                <x-dashboard.form.input-text error-key="departure_location" 
                                                             name="departure_location" 
                                                             id="departure_location" 
                                                             :value="old('departure_location')" 
                                                             label-title="Departure Location"/>

                                <x-dashboard.form.input-text error-key="arrival_location" 
                                                             name="arrival_location" 
                                                             id="arrival_location" 
                                                             :value="old('arrival_location')" 
                                                             label-title="Arrival Location"/>

                                <x-dashboard.form.input-text error-key="schedule_notes" 
                                                             name="schedule_notes" 
                                                             id="schedule_notes" 
                                                             :value="old('schedule_notes')" 
                                                             label-title="Schedule Notes"/>
                            </div>

                            <!-- Contact Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-4' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-4' }}-tab">

                                <x-dashboard.form.input-text error-key="phone_contact" 
                                                             name="phone_contact" 
                                                             id="phone_contact" 
                                                             :value="old('phone_contact')" 
                                                             label-title="Phone Contact"/>

                                <x-dashboard.form.input-text error-key="whatsapp_contact" 
                                                             name="whatsapp_contact" 
                                                             id="whatsapp_contact" 
                                                             :value="old('whatsapp_contact')" 
                                                             label-title="WhatsApp Contact"/>

                                <x-dashboard.form.input-text error-key="email_contact" 
                                                             name="email_contact" 
                                                             id="email_contact" 
                                                             :value="old('email_contact')" 
                                                             label-title="Email Contact"
                                                             type="email"/>

                                <x-dashboard.form.input-text error-key="contact_notes" 
                                                             name="contact_notes" 
                                                             id="contact_notes" 
                                                             :value="old('contact_notes')" 
                                                             label-title="Contact Notes"/>
                            </div>

                            <!-- Media Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-5' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-5' }}-tab">

                                <x-dashboard.form.media
                                    name="featured_image"
                                    title="Add Featured Image"
                                    :images="old('featured_image')"
                                />

                                <x-dashboard.form.media
                                    name="images[]"
                                    title="Add Gallery Images"
                                    :images="old('images')"
                                    :multiple="true"
                                />

                                <x-dashboard.form.media
                                    name="vehicle_images[]"
                                    title="Add Vehicle Images"
                                    :images="old('vehicle_images')"
                                    :multiple="true"
                                />

                                <x-dashboard.form.input-text error-key="route_map" 
                                                             name="route_map" 
                                                             id="route_map" 
                                                             :value="old('route_map')" 
                                                             label-title="Route Map URL"/>
                            </div>

                            <!-- SEO Tab -->
                            <div class="tab-pane fade"
                                 id="{{ 'transport-details-6' }}" role="tabpanel"
                                 aria-labelledby="{{ 'transport-details-6' }}-tab">

                                <x-dashboard.form.seo-form/>
                            </div>
                        </x-dashboard.form.multi-tab-card>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </form>
@endsection
