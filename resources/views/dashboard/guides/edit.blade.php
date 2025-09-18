@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Guide">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.guides.index') }}">Guides</a></li>
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
                            <h5>Edit Guide: {{ $guide->name }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.guides.update', $guide) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Basic Information -->
                                <h6 class="text-primary mb-3">Basic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Guide Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $guide->name) }}" required>
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
                                                    <option value="{{ $city->id }}" {{ old('city_id', $guide->city_id) == $city->id ? 'selected' : '' }}>
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
                                                   id="email" name="email" value="{{ old('email', $guide->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $guide->phone) }}" required>
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
                                                   id="nationality" name="nationality" value="{{ old('nationality', $guide->nationality) }}">
                                            @error('nationality')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="experience_years" class="form-label">Experience Years <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('experience_years') is-invalid @enderror" 
                                                   id="experience_years" name="experience_years" value="{{ old('experience_years', $guide->experience_years) }}" min="0" required>
                                            @error('experience_years')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Languages -->
                                <div class="mb-3">
                                    <label for="languages" class="form-label">Languages <span class="text-danger">*</span></label>
                                    @php
                                        $guideLanguages = is_string($guide->languages) ? json_decode($guide->languages, true) : $guide->languages;
                                        $oldLanguages = old('languages', $guideLanguages ?? []);
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
                                <div class="mb-3">
                                    <label for="specializations" class="form-label">Specializations</label>
                                    @php
                                        $guideSpecializations = is_string($guide->specializations) ? json_decode($guide->specializations, true) : $guide->specializations;
                                        $oldSpecializations = old('specializations', $guideSpecializations ?? []);
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="city_tours" 
                                                       id="spec_city_tours" {{ in_array('city_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_city_tours">City Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="historical_sites" 
                                                       id="spec_historical" {{ in_array('historical_sites', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_historical">Historical Sites</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="nature_tours" 
                                                       id="spec_nature" {{ in_array('nature_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_nature">Nature Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="adventure" 
                                                       id="spec_adventure" {{ in_array('adventure', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_adventure">Adventure</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="cultural_tours" 
                                                       id="spec_cultural" {{ in_array('cultural_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_cultural">Cultural Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="food_tours" 
                                                       id="spec_food" {{ in_array('food_tours', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_food">Food Tours</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="museums" 
                                                       id="spec_museums" {{ in_array('museums', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_museums">Museums</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="specializations[]" value="art_galleries" 
                                                       id="spec_art" {{ in_array('art_galleries', $oldSpecializations) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="spec_art">Art Galleries</label>
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
                                                   id="price_per_hour" name="price_per_hour" value="{{ old('price_per_hour', $guide->price_per_hour) }}" min="0">
                                            @error('price_per_hour')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_per_day" class="form-label">Price Per Day</label>
                                            <input type="number" step="0.01" class="form-control @error('price_per_day') is-invalid @enderror" 
                                                   id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $guide->price_per_day) }}" min="0">
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
                                                <option value="USD" {{ old('currency', $guide->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                                <option value="EUR" {{ old('currency', $guide->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                <option value="GBP" {{ old('currency', $guide->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Bio and Certifications -->
                                <h6 class="text-primary mb-3 mt-4">Professional Information</h6>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" 
                                              id="bio" name="bio" rows="4">{{ old('bio', $guide->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="certifications" class="form-label">Certifications</label>
                                    @php
                                        $guideCertifications = is_string($guide->certifications) ? json_decode($guide->certifications, true) : $guide->certifications;
                                        $oldCertifications = old('certifications', $guideCertifications ?? []);
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
                                                <input class="form-check-input" type="checkbox" name="certifications[]" value="Cultural Heritage" 
                                                       id="cert_cultural" {{ in_array('Cultural Heritage', $oldCertifications) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cert_cultural">Cultural Heritage</label>
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
                                                   id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $guide->emergency_contact) }}">
                                            @error('emergency_contact')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emergency_phone" class="form-label">Emergency Contact Phone</label>
                                            <input type="text" class="form-control @error('emergency_phone') is-invalid @enderror" 
                                                   id="emergency_phone" name="emergency_phone" value="{{ old('emergency_phone', $guide->emergency_phone) }}">
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
                                            $guideAvailability = is_string($guide->availability_schedule) ? json_decode($guide->availability_schedule, true) : $guide->availability_schedule;
                                            $oldAvailability = old('availability_schedule', $guideAvailability ?? []);
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
                                                    <option value="{{ $value }}" {{ old('status', $guide->status->value) == $value ? 'selected' : '' }}>
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
                                                   {{ old('active', $guide->active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled', $guide->enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3">{{ old('notes', $guide->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="text-end">
                                    <a href="{{ route('dashboard.guides.index') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Guide</button>
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
