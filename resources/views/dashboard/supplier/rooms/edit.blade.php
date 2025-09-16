@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('supplier.rooms.update' , $room) }}" method="POST" class="page-body">
        @csrf
        @method('PUT')

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Room" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('supplier.rooms.index') }}">Rooms</a>
            </li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <x-dashboard.partials.message-alert/>

                <div class="card tab2-card">
                    <div class="card-body needs-validation">
                        <x-dashboard.form.language-multi-tab-card tab-id="hotels">
                            @foreach(config('translatable.supported_locales') as $localKey => $local)
                                <div @class(['tab-pane fade', 'active show' => $localKey == config('app.locale')])
                                     id="{{ 'hotels-'.$localKey }}" role="tabpanel"
                                     aria-labelledby="{{ 'hotels-'.$localKey }}-tab">
                                    <x-dashboard.form.input-text error-key="{{$localKey}}.name"
                                                                 name="{{$localKey}}[name]"
                                                                 :value="old($localKey.'.name', $room->translateOrNew($localKey)->name)"
                                                                 id="{{$localKey}}-name" label-title="Name"/>

                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.description"
                                                                   name="{{$localKey}}[description]"
                                                                   :value="old($localKey.'.description', $room->translateOrNew($localKey)->description)"
                                                                   id="{{$localKey}}-description"
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
                            :tabs="['featured', 'media']"
                            tab-id="featured-media">
                            <div class="tab-pane fade active show"
                                 id="{{ 'featured-media-0' }}" role="tabpanel"
                                 aria-labelledby="{{ 'featured-media-0' }}-tab">

                                <x-dashboard.form.input-text error-key="slug"
                                                             :value="old('slug', $room->slug)"
                                                             name="slug" id="slug" label-title="Slug"
                                                             placeholder="Leave empty for automatic generation from name"/>

                                <x-dashboard.form.input-select
                                    name="supplier_hotel_id"
                                    :options="$hotels"
                                    track-by="id"
                                    option-lable="name"
                                    label-title="Hotel"
                                    :value="old('supplier_hotel_id', $room->supplier_hotel_id)"
                                    id="supplier_hotel_id"
                                    error-key="supplier_hotel_id"/>

                                <x-dashboard.form.input-select
                                    name="amenities[]"
                                    multible
                                    :value="old('amenities', $room->amenities ? $room->amenities->pluck('id')->toArray() : [])"
                                    :options="$amenities"
                                    track-by="id"
                                    option-lable="name"
                                    label-title="Amenities"
                                    id="amenities"
                                    error-key="amenities"/>

                                <x-dashboard.form.input-checkbox resource-name="Room"
                                                                 :value="old('enabled', $room->enabled)"
                                                                 error-key="enabled"
                                                                 name="enabled" id="enabled"
                                                                 label-title="Enabled"/>

                                <x-dashboard.form.input-text error-key="bed_count" name="bed_count" id="bed_count"
                                                             :value="old('bed_count', $room->bed_count)"
                                                             label-title="Bed Count" type="number" min="1" required/>

                                <x-dashboard.form.input-text error-key="room_type" name="room_type" id="room_type"
                                                             :value="old('room_type', $room->room_type)"
                                                             label-title="Room Type"/>

                                <x-dashboard.form.input-text error-key="max_capacity" name="max_capacity" id="max_capacity"
                                                             :value="old('max_capacity', $room->max_capacity)"
                                                             label-title="Max Capacity"/>

                                <x-dashboard.form.input-text error-key="bed_types" name="bed_types" id="bed_types"
                                                             :value="old('bed_types', $room->bed_types)"
                                                             label-title="Bed Types"/>

                                <x-dashboard.form.input-text error-key="night_price" name="night_price" id="night_price"
                                                             :value="old('night_price', $room->night_price)"
                                                             label-title="Night Price" type="number" step="0.01" min="0" required/>

                                <!-- Extra Bed Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="mb-3">Extra Bed Configuration</h6>
                                    </div>
                                </div>

                                <x-dashboard.form.input-checkbox resource-name="Extra Bed"
                                                                 :value="old('extra_bed_available', $room->extra_bed_available)"
                                                                 error-key="extra_bed_available"
                                                                 name="extra_bed_available" id="extra_bed_available"
                                                                 label-title="Extra Bed Available"/>

                                <x-dashboard.form.input-text error-key="extra_bed_price" name="extra_bed_price" id="extra_bed_price"
                                                             :value="old('extra_bed_price', $room->extra_bed_price)"
                                                             label-title="Extra Bed Price (per night)"
                                                             type="number"
                                                             step="0.01"
                                                             min="0"/>

                                <x-dashboard.form.input-text error-key="max_extra_beds" name="max_extra_beds" id="max_extra_beds"
                                                             :value="old('max_extra_beds', $room->max_extra_beds)"
                                                             label-title="Maximum Extra Beds"
                                                             type="number"
                                                             min="0"
                                                             max="5"/>

                                <x-dashboard.form.input-textarea error-key="extra_bed_description" name="extra_bed_description" id="extra_bed_description"
                                                                 :value="old('extra_bed_description', $room->extra_bed_description)"
                                                                 label-title="Extra Bed Description"
                                                                 placeholder="Describe the extra bed service..."/>

                            </div>
                            <div class="tab-pane fade"
                                 id="{{ 'featured-media-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'featured-media-1' }}-tab">
                                <x-dashboard.form.media title="Add Banner Image"
                                                        :images="old('banner', $room->banner)"
                                                        name="banner"/>

                                <x-dashboard.form.media title="Add Featured Image"
                                                        :images="old('featured_image', $room->featured_image)"
                                                        name="featured_image"/>

                                <x-dashboard.form.media title="Add Gallery" :multiple="true"
                                                        :images="old('gallery', $room->gallery)"
                                                        name="gallery[]"/>
                            </div>
                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <!--Start SEO-->
                <x-dashboard.form.seo-form :seo="$room->seo" />
                <!--End SEO-->

            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const extraBedCheckbox = document.getElementById('extra_bed_available');
    const extraBedPrice = document.getElementById('extra_bed_price');
    const maxExtraBeds = document.getElementById('max_extra_beds');
    const extraBedDescription = document.getElementById('extra_bed_description');

    function toggleExtraBedFields() {
        const isEnabled = extraBedCheckbox.checked;
        extraBedPrice.disabled = !isEnabled;
        maxExtraBeds.disabled = !isEnabled;
        extraBedDescription.disabled = !isEnabled;
        
        if (!isEnabled) {
            extraBedPrice.value = '';
            maxExtraBeds.value = '1';
            extraBedDescription.value = '';
        }
    }

    extraBedCheckbox.addEventListener('change', toggleExtraBedFields);
    toggleExtraBedFields(); // Initial state - this will set the correct disabled state based on current checkbox value
});
</script>
@endsection
