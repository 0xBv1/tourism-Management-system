@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Representative">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.representatives.index') }}">Representatives</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Edit Representative: {{ $representative->name }}</h5>
                            @if(admin()->roles->count() > 0)
                                <small class="text-muted">
                                    <i class="fa fa-user"></i> 
                                    Role: {{ admin()->roles->pluck('name')->join(', ') }}
                                </small>
                            @endif
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.representatives.update', $representative) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Basic Information -->
                                <h6 class="text-primary mb-3">Basic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Representative Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $representative->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city_id" class="form-label">City <span class="text-danger">*</span></label>
                                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                                    id="city_id" name="city_id" required>
                                                <option value="">Select City</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id', $representative->city_id) == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('city_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $representative->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $representative->phone) }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nationality" class="form-label">Nationality</label>
                                            <input type="text" class="form-control @error('nationality') is-invalid @enderror" 
                                                   id="nationality" name="nationality" value="{{ old('nationality', $representative->nationality) }}">
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="experience_years" class="form-label">Experience Years <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                                                   id="experience_years" name="experience_years" value="{{ old('experience_years', $representative->experience_years) }}" min="0" required>
                                            @error('experience_years')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Company Information -->
                                <h6 class="text-primary mb-3 mt-4">Company Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" name="company_name" value="{{ old('company_name', $representative->company_name) }}">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="company_license" class="form-label">Company License</label>
                                            <input type="text" class="form-control @error('company_license') is-invalid @enderror" 
                                                   id="company_license" name="company_license" value="{{ old('company_license', $representative->company_license) }}">
                                            @error('company_license')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Languages -->
                                <h6 class="text-primary mb-3 mt-4">Languages</h6>
                                <div class="mb-3">
                                    <label for="languages" class="form-label">Languages <span class="text-danger">*</span></label>
                                    @php
                                        $representativeLanguages = is_string($representative->languages) ? json_decode($representative->languages, true) : $representative->languages;
                                        $oldLanguages = old('languages', $representativeLanguages ?? []);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="English" 
                                                       id="lang_english" {{ in_array('English', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_english">English</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="Spanish" 
                                                       id="lang_spanish" {{ in_array('Spanish', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_spanish">Spanish</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="French" 
                                                       id="lang_french" {{ in_array('French', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_french">French</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="German" 
                                                       id="lang_german" {{ in_array('German', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_german">German</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="Italian" 
                                                       id="lang_italian" {{ in_array('Italian', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_italian">Italian</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="Portuguese" 
                                                       id="lang_portuguese" {{ in_array('Portuguese', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_portuguese">Portuguese</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="Chinese" 
                                                       id="lang_chinese" {{ in_array('Chinese', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_chinese">Chinese</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="languages[]" value="Japanese" 
                                                       id="lang_japanese" {{ in_array('Japanese', $oldLanguages) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="lang_japanese">Japanese</label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('languages')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Specializations -->
                                <h6 class="text-primary mb-3 mt-4">Specializations</h6>
                                <div class="mb-3">
                                    <label for="specializations" class="form-label">Specializations</label>
                                    @php
                                        $representativeSpecializations = is_string($representative->specializations) ? json_decode($representative->specializations, true) : $representative->specializations;
                                        $oldSpecializations = old('specializations', $representativeSpecializations ?? []);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="group_tours" 
                                                       id="spec_group_tours" {{ in_array('group_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_group_tours">Group Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="corporate_events" 
                                                       id="spec_corporate" {{ in_array('corporate_events', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_corporate">Corporate Events</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="cultural_tours" 
                                                       id="spec_cultural" {{ in_array('cultural_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_cultural">Cultural Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="museums" 
                                                       id="spec_museums" {{ in_array('museums', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_museums">Museums</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="art_galleries" 
                                                       id="spec_art" {{ in_array('art_galleries', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_art">Art Galleries</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="business_tours" 
                                                       id="spec_business" {{ in_array('business_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_business">Business Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="educational_tours" 
                                                       id="spec_educational" {{ in_array('educational_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_educational">Educational Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="luxury_tours" 
                                                       id="spec_luxury" {{ in_array('luxury_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_luxury">Luxury Tours</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Areas -->
                                <h6 class="text-primary mb-3 mt-4">Service Areas</h6>
                                <div class="mb-3">
                                    <label for="service_areas" class="form-label">Service Areas</label>
                                    @php
                                        $representativeServiceAreas = is_string($representative->service_areas) ? json_decode($representative->service_areas, true) : $representative->service_areas;
                                        $oldServiceAreas = old('service_areas', $representativeServiceAreas ?? []);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="city_center" 
                                                       id="area_city_center" {{ in_array('city_center', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_city_center">City Center</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="historical_district" 
                                                       id="area_historical" {{ in_array('historical_district', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_historical">Historical District</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="business_district" 
                                                       id="area_business" {{ in_array('business_district', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_business">Business District</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="airport" 
                                                       id="area_airport" {{ in_array('airport', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_airport">Airport</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="hotels" 
                                                       id="area_hotels" {{ in_array('hotels', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_hotels">Hotels</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="convention_center" 
                                                       id="area_convention" {{ in_array('convention_center', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_convention">Convention Center</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="shopping_areas" 
                                                       id="area_shopping" {{ in_array('shopping_areas', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_shopping">Shopping Areas</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="service_areas[]" value="entertainment_district" 
                                                       id="area_entertainment" {{ in_array('entertainment_district', $oldServiceAreas) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="area_entertainment">Entertainment District</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Information -->
                                <h6 class="text-primary mb-3 mt-4">Pricing Information</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_hour" class="form-label">Price Per Hour</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_hour') is-invalid @enderror" 
                                                   id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour', $representative->price_per_hour) }}" min="0">
                                            @error('price_per_hour')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_day" class="form-label">Price Per Day</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_day') is-invalid @enderror" 
                                                   id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $representative->price_per_day) }}" min="0">
                                            @error('price_per_day')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="form-control @error('currency') is-invalid @enderror" 
                                                    id="currency" name="currency" required>
                                                <option value="">Select Currency</option>
                                                <option value="USD" {{ old('currency', $representative->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ old('currency', $representative->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ old('currency', $representative->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Information -->
                                <h6 class="text-primary mb-3 mt-4">Professional Information</h6>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" name="bio" rows="4">{{ old('bio', $representative->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="certifications" class="form-label">Certifications</label>
                                    @php
                                        $representativeCertifications = is_string($representative->certifications) ? json_decode($representative->certifications, true) : $representative->certifications;
                                        $oldCertifications = old('certifications', $representativeCertifications ?? []);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="certifications[]" value="Tour Guide License" 
                                                       id="cert_license" {{ in_array('Tour Guide License', $oldCertifications) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cert_license">Tour Guide License</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="certifications[]" value="First Aid Certified" 
                                                       id="cert_first_aid" {{ in_array('First Aid Certified', $oldCertifications) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cert_first_aid">First Aid Certified</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="certifications[]" value="Language Proficiency" 
                                                       id="cert_language" {{ in_array('Language Proficiency', $oldCertifications) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cert_language">Language Proficiency</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="certifications[]" value="Business License" 
                                                       id="cert_business" {{ in_array('Business License', $oldCertifications) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cert_business">Business License</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact -->
                                <h6 class="text-primary mb-3 mt-4">Emergency Contact</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emergency_contact" class="form-label">Emergency Contact Name</label>
                                            <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                                   id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $representative->emergency_contact) }}">
                                            @error('emergency_contact')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emergency_phone" class="form-label">Emergency Contact Phone</label>
                                            <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                                   id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $representative->emergency_phone) }}">
                                            @error('emergency_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Availability Schedule -->
                                <h6 class="text-primary mb-3 mt-4">Availability Schedule</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        @php
                                            $representativeAvailability = is_string($representative->availability_schedule) ? json_decode($representative->availability_schedule, true) : $representative->availability_schedule;
                                            $oldAvailability = old('availability_schedule', $representativeAvailability ?? []);
                                        @endphp
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[monday]" value="1" 
                                                           id="avail_monday" {{ $oldAvailability['monday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_monday">Monday</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[tuesday]" value="1" 
                                                           id="avail_tuesday" {{ $oldAvailability['tuesday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_tuesday">Tuesday</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[wednesday]" value="1" 
                                                           id="avail_wednesday" {{ $oldAvailability['wednesday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_wednesday">Wednesday</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[thursday]" value="1" 
                                                           id="avail_thursday" {{ $oldAvailability['thursday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_thursday">Thursday</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[friday]" value="1" 
                                                           id="avail_friday" {{ $oldAvailability['friday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_friday">Friday</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[saturday]" value="1" 
                                                           id="avail_saturday" {{ $oldAvailability['saturday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_saturday">Saturday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="availability_schedule[sunday]" value="1" 
                                                           id="avail_sunday" {{ $oldAvailability['sunday'] ?? false ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="avail_sunday">Sunday</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Notes -->
                                <h6 class="text-primary mb-3 mt-4">Status and Additional Information</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                @foreach($statuses as $value => $label)
                                                    <option value="{{ $value }}" {{ old('status', $representative->status->value) == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                                   {{ old('active', $representative->active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled', $representative->enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $representative->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('dashboard.representatives.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Representative</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Container-fluid Ends-->
    </div>
@endsection
