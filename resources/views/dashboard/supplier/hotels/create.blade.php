@extends('layouts.dashboard.app')

@section('content')
    <form action="{{ route('supplier.hotels.store' ) }}" method="POST" class="page-body">
        @csrf

        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Create Hotel" :hideFirst="true">
            <li class="breadcrumb-item">
                <a href="{{ route('supplier.hotels.index') }}">Hotels</a>
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
                                                                 name="{{$localKey}}[name]" id="{{$localKey}}-name"
                                                                 :value="old($localKey.'.name')"
                                                                 label-title="Name"/>

                                    <x-dashboard.form.input-editor error-key="{{$localKey}}.description"
                                                                 name="{{$localKey}}[description]"
                                                                 id="{{$localKey}}-description"
                                                                 :value="old($localKey.'.description')"
                                                                 label-title="Description"/>

                                    <x-dashboard.form.input-select
                                        name="{{$localKey}}[city]"
                                        :value="old($localKey.'.city')"
                                        :options="$cities"
                                        label-title="City"
                                        id="{{$localKey}}-city"
                                        error-key="{{$localKey}}.city"/>


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
                                                             name="slug" id="slug"
                                                             :value="old('slug')"
                                                             label-title="Slug"
                                                             placeholder="Leave empty for automatic generation from name"/>

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

                                <x-dashboard.form.input-text error-key="stars" name="stars" id="stars" :value="old('stars')" label-title="Stars"/>

                                <x-dashboard.form.input-text error-key="address" name="address" id="address"
                                                             :value="old('address')" label-title="Address"/>

                                <x-dashboard.form.input-text error-key="map_iframe" name="map_iframe" id="map_iframe"
                                                             :value="old('map_iframe')" label-title="Map Iframe"/>


                                <x-dashboard.form.input-text error-key="phone_contact" name="phone_contact" id="phone_contact"
                                                             :value="old('phone_contact')" label-title="Phone Contact"/>

                                <x-dashboard.form.input-text error-key="whatsapp_contact" name="whatsapp_contact"
                                                             id="whatsapp_contact" :value="old('whatsapp_contact')" label-title="Whatsapp Contact"/>



                                <x-dashboard.form.input-checkbox resource-name="Hotel" :value="true"
                                                                 error-key="enabled"
                                                                 name="enabled" id="enabled"
                                                                 label-title="Enabled"/>


                            </div>
                            <div class="tab-pane fade"
                                 id="{{ 'featured-media-1' }}" role="tabpanel"
                                 aria-labelledby="{{ 'featured-media-1' }}-tab">
                                <x-dashboard.form.media title="Add Banner Image"
                                                        :images="old('banner')"
                                                        name="banner"/>

                                <x-dashboard.form.media title="Add Featured Image"
                                                        :images="old('featured_image')"
                                                        name="featured_image"/>

                                <x-dashboard.form.media title="Add Gallery" :multiple="true"
                                                        :images="old('gallery')"
                                                        name="gallery[]"/>
                            </div>
                        </x-dashboard.form.multi-tab-card>

                        <x-dashboard.form.submit-button/>
                    </div>
                </div>

                <!--Start SEO-->
                <x-dashboard.form.seo-form/>
                <!--End SEO-->


            </div>
        </div>
        <!-- Container-fluid Ends-->

    </form>
@endsection
