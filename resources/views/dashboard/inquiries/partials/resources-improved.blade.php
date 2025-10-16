@if(admin()->hasRole(['Sales' ,'Finance' ]) || Gate::allows('manage-inquiry-resources'))
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            @if(Gate::allows('manage-inquiry-resources'))
                <i class="fas fa-cogs me-2"></i>Resources Management
            @else
                <i class="fas fa-eye me-2"></i>Assigned Resources
            @endif
        </h5>
    </div>
    <div class="card-body">
        @if(Gate::allows('manage-inquiry-resources'))
        @php
            $user = auth()->user();
            $isOperator = $user->hasRole('Operator');
            $isReservation = $user->hasRole('Reservation');
            $isAdmin = $user->hasRole(['Admin', 'Administrator']);
            
            // Determine which tabs to show based on role
            // $showHotels = $isReservation || $isAdmin;
            // $showVehicles = $isOperator || $isAdmin;
            // $showGuides = $isOperator || $isAdmin;
            // $showRepresentatives = $isOperator || $isAdmin;
            // $showExtraServices = ($isOperator || $isReservation) || $isAdmin;
            // $showTickets = $isReservation || $isAdmin;
            // $showDahabias = $isReservation || $isAdmin;
            // $showRestaurants = $isReservation || $isAdmin;
            

            $showHotels = $isReservation ||$isOperator || $isAdmin;
            $showVehicles =$isReservation ||$isOperator || $isAdmin;
            $showGuides = $isReservation ||$isOperator || $isAdmin;
            $showRepresentatives = $isReservation ||$isOperator || $isAdmin;
            $showExtraServices = $isReservation ||$isOperator || $isAdmin;
            $showTickets = $isReservation ||$isOperator || $isAdmin;
            $showDahabias = $isReservation ||$isOperator || $isAdmin;
            $showRestaurants = $isReservation ||$isOperator || $isAdmin;
            // Determine active tab (first visible tab)
            $activeTabSet = false;
            $firstTabId = '';
            if ($showHotels && !$activeTabSet) {
                $firstTabId = 'hotel';
                $activeTabSet = true;
            } elseif ($showVehicles && !$activeTabSet) {
                $firstTabId = 'vehicle';
                $activeTabSet = true;
            } elseif ($showGuides && !$activeTabSet) {
                $firstTabId = 'guide';
                $activeTabSet = true;
            } elseif ($showRepresentatives && !$activeTabSet) {
                $firstTabId = 'representative';
                $activeTabSet = true;
            } elseif ($showExtraServices && !$activeTabSet) {
                $firstTabId = 'extra';
                $activeTabSet = true;
            } elseif ($showTickets && !$activeTabSet) {
                $firstTabId = 'ticket';
                $activeTabSet = true;
            } elseif ($showDahabias && ! $activeTabSet) {
                $firstTabId = 'dahabia';
                $activeTabSet = true;
            } elseif ($showRestaurants && !$activeTabSet) {
                $firstTabId = 'restaurant';
                $activeTabSet = true;
            }
        @endphp
        
        <!-- Resource Type Navigation -->
        <ul class="nav nav-tabs mb-4" id="resourceTabs" role="tablist">
            @if($showHotels)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'hotel' ? 'active' : '' }}" id="hotel-tab" data-bs-toggle="tab" data-bs-target="#hotel" type="button" role="tab" aria-controls="hotel" aria-selected="{{ $activeTabSet && $firstTabId === 'hotel' ? 'true' : 'false' }}">
                    <i class="fas fa-hotel me-1"></i>Hotels
                </button>
            </li>
            @endif
            
            @if($showVehicles)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'vehicle' ? 'active' : '' }}" id="vehicle-tab" data-bs-toggle="tab" data-bs-target="#vehicle" type="button" role="tab" aria-controls="vehicle" aria-selected="{{ $activeTabSet && $firstTabId === 'vehicle' ? 'true' : 'false' }}">
                    <i class="fas fa-car me-1"></i>Vehicles
                </button>
            </li>
            @endif
            
            @if($showGuides)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'guide' ? 'active' : '' }}" id="guide-tab" data-bs-toggle="tab" data-bs-target="#guide" type="button" role="tab" aria-controls="guide" aria-selected="{{ $activeTabSet && $firstTabId === 'guide' ? 'true' : 'false' }}">
                    <i class="fas fa-user-tie me-1"></i>Guides
                </button>
            </li>
            @endif
            
            @if($showRepresentatives)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'representative' ? 'active' : '' }}" id="representative-tab" data-bs-toggle="tab" data-bs-target="#representative" type="button" role="tab" aria-controls="representative" aria-selected="{{ $activeTabSet && $firstTabId === 'representative' ? 'true' : 'false' }}">
                    <i class="fas fa-handshake me-1"></i>Representatives
                </button>
            </li>
            @endif
            
            @if($showExtraServices)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'extra' ? 'active' : '' }}" id="extra-tab" data-bs-toggle="tab" data-bs-target="#extra" type="button" role="tab" aria-controls="extra" aria-selected="{{ $activeTabSet && $firstTabId === 'extra' ? 'true' : 'false' }}">
                    <i class="fas fa-plus-circle me-1"></i>Extra Services
                </button>
            </li>
            @endif
            
            @if($showTickets)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'ticket' ? 'active' : '' }}" id="ticket-tab" data-bs-toggle="tab" data-bs-target="#ticket" type="button" role="tab" aria-controls="ticket" aria-selected="{{ $activeTabSet && $firstTabId === 'ticket' ? 'true' : 'false' }}">
                    <i class="fas fa-ticket-alt me-1"></i>Tickets
                </button>
            </li>
            @endif
            
            @if($showDahabias)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'dahabia' ? 'active' : '' }}" id="dahabia-tab" data-bs-toggle="tab" data-bs-target="#dahabia" type="button" role="tab" aria-controls="dahabia" aria-selected="{{ $activeTabSet && $firstTabId === 'dahabia' ? 'true' : 'false' }}">
                    <i class="fas fa-sailboat me-1"></i>Dahabias
                </button>
            </li>
            @endif
            
            @if($showRestaurants)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTabSet && $firstTabId === 'restaurant' ? 'active' : '' }}" id="restaurant-tab" data-bs-toggle="tab" data-bs-target="#restaurant" type="button" role="tab" aria-controls="restaurant" aria-selected="{{ $activeTabSet && $firstTabId === 'restaurant' ? 'true' : 'false' }}">
                    <i class="fas fa-utensils me-1"></i>Restaurants
                </button>
            </li>
            @endif
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="resourceTabContent">
            <!-- Hotel Tab -->
            @if($showHotels)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'hotel' ? 'show active' : '' }}" id="hotel" role="tabpanel" aria-labelledby="hotel-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="hotel_select" class="form-label">Select Hotel</label>
                        <select class="form-select" id="hotel_select" name="hotel_id">
                            <option value="">Choose a hotel...</option>
                            @foreach($availableResources['hotels'] as $hotel)
                                @php
                                    $currency = $hotel->currency ?? '$';
                                    $ppn = $hotel->price_per_night ?? null;
                                    $priceText = $ppn ? (' - ' . $currency . ' ' . number_format((float)$ppn, 2) . ' /night') : '';
                                @endphp
                                <option value="{{ $hotel->id }}" data-price-per-night="{{ $hotel->price_per_night ?? '' }}" data-currency="{{ $hotel->currency ?? '' }}">
                                    {{ $hotel->name }}@if($hotel->city) ({{ $hotel->city->name }})@endif{!! $priceText !!}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addHotelBtn" >
                                <i class="fas fa-plus me-1"></i>Add Hotel
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="hotel_check_in" class="form-label">Check-in</label>
                        <input type="date" class="form-control" id="hotel_check_in" />
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_check_out" class="form-label">Check-out</label>
                        <input type="date" class="form-control" id="hotel_check_out" />
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_number_of_rooms" class="form-label">Rooms</label>
                        <input type="number" min="1" step="1" class="form-control" id="hotel_number_of_rooms" placeholder="e.g. 1" />
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_number_of_adults" class="form-label">Adults</label>
                        <input type="number" min="0" step="1" class="form-control" id="hotel_number_of_adults" placeholder="e.g. 2" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="hotel_number_of_children" class="form-label">Children</label>
                        <input type="number" min="0" step="1" class="form-control" id="hotel_number_of_children" placeholder="e.g. 1" />
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_rate_per_adult" class="form-label">Rate/Adult</label>
                        <div class="input-group">
                            <span class="input-group-text" id="hotel_currency_badge_adult">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="hotel_rate_per_adult" placeholder="Optional" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_rate_per_child" class="form-label">Rate/Child</label>
                        <div class="input-group">
                            <span class="input-group-text" id="hotel_currency_badge_child">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="hotel_rate_per_child" placeholder="Optional" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="hotel_price_type" class="form-label">Price Type</label>
                        <select id="hotel_price_type" class="form-select">
                            <option value="day" selected>Per Night</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_new_price" class="form-label">New Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="hotel_currency_badge_new">$</span>
                            <input type="number" step="0.5" class="form-control " style="width: 30%;" id="hotel_new_price" placeholder="Enter new price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="hotel_increase_percent" class="form-label">Increase By (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" class="form-control w-22px" id="hotel_increase_percent" placeholder="e.g. 10" />
                            <button type="button" class="btn btn-outline-secondary" id="hotel_increase_btn">Increase Price</button>
                        </div>
                    </div>
                </div>
                
                <!-- Allow Multiple Resources Option -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hotel_allow_multiple" name="allow_multiple">
                            <label class="form-check-label" for="hotel_allow_multiple">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Allow adding the same resource multiple times (bypass duplicate check)
                                </small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Vehicle Tab -->
            @if($showVehicles)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'vehicle' ? 'show active' : '' }}" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="vehicle_select" class="form-label">Select Vehicle</label>
                        <select class="form-select" id="vehicle_select" name="vehicle_id">
                            <option value="">Choose a vehicle...</option>
                            @foreach($availableResources['vehicles'] as $vehicle)
                                @php
                                    $currency = $vehicle->currency ?: '$';
                                    $ppd = $vehicle->price_per_day;
                                    $pph = $vehicle->price_per_hour;
                                    $priceParts = [];
                                    if(!is_null($ppd) && $ppd !== '') {
                                        $priceParts[] = $currency . ' ' . number_format((float)$ppd, 2) . ' /day';
                                    }
                                    if(!is_null($pph) && $pph !== '') {
                                        $priceParts[] = $currency . ' ' . number_format((float)$pph, 2) . ' /hour';
                                    }
                                    $priceText = count($priceParts) ? ' - ' . implode(' | ', $priceParts) : '';
                                @endphp
                                <option 
                                    value="{{ $vehicle->id }}"
                                    data-price-per-day="{{ $vehicle->price_per_day }}"
                                    data-price-per-hour="{{ $vehicle->price_per_hour }}"
                                    data-currency="{{ $vehicle->currency }}">
                                    {{ $vehicle->name }}@if($vehicle->type) - {{ $vehicle->type }}@endif @if($vehicle->city) ({{ $vehicle->city->name }})@endif{!! $priceText !!}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addVehicleBtn" >
                                <i class="fas fa-plus me-1"></i>Add Vehicle
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="vehicle_from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="vehicle_from_date" />
                    </div>
                    <div class="col-md-4">
                        <label for="vehicle_from_time" class="form-label">From Time</label>
                        <input type="time" class="form-control" id="vehicle_from_time" />
                    </div>
                    <div class="col-md-4">
                        <label for="vehicle_to_time" class="form-label">To Time</label>
                        <input type="time" class="form-control" id="vehicle_to_time" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="vehicle_price_type" class="form-label">Price Type</label>
                        <select id="vehicle_price_type" class="form-select">
                            <option value="day" selected>Per Day</option>
                            <option value="hour">Per Hour</option>
                        </select>
                    </div>
                   
                    <div class="col-md-3">
                        <label for="vehicle_new_price" class="form-label">New Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="vehicle_currency_badge_new">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="vehicle_new_price" placeholder="Enter new price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="vehicle_increase_percent" class="form-label">Increase By (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" class="form-control w-22px" id="vehicle_increase_percent" placeholder="e.g. 10" />
                            <button type="button" class="btn btn-outline-secondary" id="vehicle_increase_btn">Increase Price</button>
                        </div>
                    </div>
                </div>
                
                <!-- Allow Multiple Resources Option -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="vehicle_allow_multiple" name="allow_multiple">
                            <label class="form-check-label" for="vehicle_allow_multiple">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Allow adding the same resource multiple times (bypass duplicate check)
                                </small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Guide Tab -->
            @if($showGuides)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'guide' ? 'show active' : '' }}" id="guide" role="tabpanel" aria-labelledby="guide-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="guide_select" class="form-label">Select Guide</label>
                        <select class="form-select" id="guide_select" name="guide_id">
                            <option value="">Choose a guide...</option>
                            @foreach($availableResources['guides'] as $guide)
                                <option value="{{ $guide->id }}">
                                    {{ $guide->name }}@if($guide->city) ({{ $guide->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addGuideBtn" disabled>
                                <i class="fas fa-plus me-1"></i>Add Guide
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Date and Time Section -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="guide_start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="guide_start_date" />
                    </div>
                    <div class="col-md-4">
                        <label for="guide_start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="guide_start_time" />
                    </div>
                    <div class="col-md-4">
                        <label for="guide_end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="guide_end_time" />
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="guide_price_type" class="form-label">Price Type</label>
                        <select id="guide_price_type" class="form-select">
                            <option value="day">يومية (Daily)</option>
                            <option value="half_day">نص يومية (Half Daily)</option>
                            <option value="hour">ساعية (Hourly)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="guide_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="guide_currency_badge">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="guide_price" placeholder="Enter price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="guide_currency" class="form-label">Currency</label>
                        <select id="guide_currency" class="form-select">
                            @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                <option value="{{ $currencyCode }}" {{ $currencyCode == 'EGP' ? 'selected' : '' }}>
                                    {{ $currencyCode }} - {{ $currencyName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Location and Description Section -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="guide_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="guide_location" placeholder="e.g., Pyramids of Giza, Luxor Temple" />
                    </div>
                    <div class="col-md-6">
                        <label for="guide_description" class="form-label">Description</label>
                        <textarea class="form-control" id="guide_description" rows="2" placeholder="Describe the tour or guide services"></textarea>
                    </div>
                </div>

                <!-- Language Selection Section -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Languages</label>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Available Languages</label>
                                    <select id="available-languages" class="form-select" size="6" multiple>
                                        <option value="arabic">العربية (Arabic)</option>
                                        <option value="english">English</option>
                                        <option value="french">Français (French)</option>
                                        <option value="german">Deutsch (German)</option>
                                        <option value="spanish">Español (Spanish)</option>
                                        <option value="italian">Italiano (Italian)</option>
                                        <option value="russian">Русский (Russian)</option>
                                        <option value="chinese">中文 (Chinese)</option>
                                        <option value="japanese">日本語 (Japanese)</option>
                                        <option value="korean">한국어 (Korean)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selected Languages</label>
                                    <select id="selected-languages" class="form-select" size="6" multiple>
                                        <!-- Selected languages will be shown here -->
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-language-btn">
                                        <i class="fas fa-arrow-right"></i> Add Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="remove-language-btn">
                                        <i class="fas fa-arrow-left"></i> Remove Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clear-languages-btn">
                                        <i class="fas fa-times"></i> Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hold Ctrl/Cmd to select multiple languages
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Representative Tab -->
            @if($showRepresentatives)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'representative' ? 'show active' : '' }}" id="representative" role="tabpanel" aria-labelledby="representative-tab">
                <div class="row">
                    <div class="col-md-8">
                        <label for="representative_select" class="form-label">Select Representative</label>
                        <select class="form-select" id="representative_select" name="representative_id">
                            <option value="">Choose a representative...</option>
                            @foreach($availableResources['representatives'] as $representative)
                                <option value="{{ $representative->id }}">
                                    {{ $representative->name }}@if($representative->city) ({{ $representative->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addRepresentativeBtn" disabled>
                                <i class="fas fa-plus me-1"></i>Add Representative
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Date and Time Section -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="representative_start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="representative_start_date" />
                    </div>
                    <div class="col-md-4">
                        <label for="representative_start_time" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="representative_start_time" />
                    </div>
                    <div class="col-md-4">
                        <label for="representative_end_time" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="representative_end_time" />
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="representative_price_type" class="form-label">Price Type</label>
                        <select id="representative_price_type" class="form-select">
                            <option value="day">يومية (Daily)</option>
                            <option value="half_day">نص يومية (Half Daily)</option>
                            <option value="hour">ساعية (Hourly)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="representative_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="representative_currency_badge">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="representative_price" placeholder="Enter price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="representative_currency" class="form-label">Currency</label>
                        <select id="representative_currency" class="form-select">
                            @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                <option value="{{ $currencyCode }}" {{ $currencyCode == 'EGP' ? 'selected' : '' }}>
                                    {{ $currencyCode }} - {{ $currencyName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Location and Description Section -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="representative_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="representative_location" placeholder="e.g., Airport, Hotel, Tourist Site" />
                    </div>
                    <div class="col-md-6">
                        <label for="representative_description" class="form-label">Description</label>
                        <textarea class="form-control" id="representative_description" rows="2" placeholder="Describe the representative services"></textarea>
                    </div>
                </div>

                <!-- Language Selection Section -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label class="form-label">Languages</label>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Available Languages</label>
                                    <select id="available-representative-languages" class="form-select" size="6" multiple>
                                        <option value="arabic">العربية (Arabic)</option>
                                        <option value="english">English</option>
                                        <option value="french">Français (French)</option>
                                        <option value="german">Deutsch (German)</option>
                                        <option value="spanish">Español (Spanish)</option>
                                        <option value="italian">Italiano (Italian)</option>
                                        <option value="russian">Русский (Russian)</option>
                                        <option value="chinese">中文 (Chinese)</option>
                                        <option value="japanese">日本語 (Japanese)</option>
                                        <option value="korean">한국어 (Korean)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selected Languages</label>
                                    <select id="selected-representative-languages" class="form-select" size="6" multiple>
                                        <!-- Selected languages will be shown here -->
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-representative-language-btn">
                                        <i class="fas fa-arrow-right"></i> Add Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="remove-representative-language-btn">
                                        <i class="fas fa-arrow-left"></i> Remove Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clear-representative-languages-btn">
                                        <i class="fas fa-times"></i> Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hold Ctrl/Cmd to select multiple languages
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Extra Services Tab -->
            @if($showExtraServices)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'extra' ? 'show active' : '' }}" id="extra" role="tabpanel" aria-labelledby="extra-tab">
                
                <!-- Service Name -->
                <div class="row">
                    <div class="col-md-12">
                        <label for="extra_service_name" class="form-label">Service Name</label>
                        <input type="text" class="form-control" id="extra_service_name" placeholder="Enter service name (e.g., Airport Transfer, City Tour)" />
                    </div>
                </div>

                <!-- Description -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="extra_service_description" class="form-label">Description</label>
                        <textarea class="form-control" id="extra_service_description" rows="3" placeholder="Describe the extra service details"></textarea>
                    </div>
                </div>

                <!-- Pricing Section -->
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="extra_service_price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="extra_currency_badge">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="extra_service_price" placeholder="Enter price" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="extra_service_currency" class="form-label">Currency</label>
                        <select id="extra_service_currency" class="form-select">
                            @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                <option value="{{ $currencyCode }}" {{ $currencyCode == 'USD' ? 'selected' : '' }}>
                                    {{ $currencyCode }} - {{ $currencyName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="extra_service_price_type" class="form-label">Price Type</label>
                        <select id="extra_service_price_type" class="form-select">
                            <option value="per_person" selected>Per Person</option>
                            <option value="per_group">Per Group</option>
                            <option value="per_hour">Per Hour</option>
                            <option value="per_day">Per Day</option>
                            <option value="fixed">Fixed Price</option>
                        </select>
                    </div>
                </div>

                <!-- Add Button -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary w-100" id="addExtraBtn" disabled>
                            <i class="fas fa-plus me-1"></i>Add Extra Service
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Tickets Tab -->
            @if($showTickets)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'ticket' ? 'show active' : '' }}" id="ticket" role="tabpanel" aria-labelledby="ticket-tab">

                <div class="row">
                    <div class="col-md-8">
                        <label for="ticket_select" class="form-label">Select Ticket</label>
                        <select class="form-select" id="ticket_select" name="ticket_id">
                            <option value="">Choose a ticket...</option>
                            @foreach($availableResources['tickets'] as $ticket)
                                @php
                                    $currency = $ticket->currency ?: '$';
                                    $price = $ticket->price_per_person ?? null;
                                    $priceText = $price ? (' - ' . $currency . ' ' . number_format((float)$price, 2) . ' /person') : '';
                                @endphp
                                <option value="{{ $ticket->id }}" data-price-per-person="{{ $ticket->price_per_person ?? '' }}" data-currency="{{ $ticket->currency ?? '' }}">
                                    {{ $ticket->name }}@if($ticket->city) ({{ $ticket->city->name }})@endif{!! $priceText !!}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addTicketBtn" >
                                <i class="fas fa-plus me-1"></i>Add Ticket
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="ticket_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="ticket_date" />
                    </div>
                    <div class="col-md-3">
                        <label for="ticket_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="ticket_time" />
                    </div>
                    <div class="col-md-3">
                        <label for="ticket_quantity" class="form-label">Quantity</label>
                        <input type="number" min="1" step="1" class="form-control" id="ticket_quantity" placeholder="e.g. 2" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="ticket_price_type" class="form-label">Price Type</label>
                        <select id="ticket_price_type" class="form-select">
                            <option value="person" selected>Per Person</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="ticket_new_price" class="form-label">New Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="ticket_currency_badge_new">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="ticket_new_price" placeholder="Enter new price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="ticket_increase_percent" class="form-label">Increase By (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" class="form-control w-22px" id="ticket_increase_percent" placeholder="e.g. 10" />
                            <button type="button" class="btn btn-outline-secondary" id="ticket_increase_btn">Increase Price</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif


            <!-- Dahabia Tab -->
            @if($showDahabias)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'dahabia' ? 'show active' : '' }}" id="dahabia" role="tabpanel" aria-labelledby="dahabia-tab">

                <div class="row">
                    <div class="col-md-8">
                        <label for="dahabia_select" class="form-label">Select Dahabia</label>
                        <select class="form-select" id="dahabia_select" name="dahabia_id">
                            <option value="">Choose a dahabia...</option>
                            @foreach($availableResources['dahabias'] as $dahabia)
                                @php
                                    $currency = $dahabia->currency ?: '$';
                                    $pricePerPerson = $dahabia->price_per_person ?? null;
                                    $pricePerCharter = $dahabia->price_per_charter ?? null;
                                    $priceParts = [];
                                    if(!is_null($pricePerPerson) && $pricePerPerson !== '') {
                                        $priceParts[] = $currency . ' ' . number_format((float)$pricePerPerson, 2) . ' /person';
                                    }
                                    if(!is_null($pricePerCharter) && $pricePerCharter !== '') {
                                        $priceParts[] = $currency . ' ' . number_format((float)$pricePerCharter, 2) . ' /charter';
                                    }
                                    $priceText = count($priceParts) ? ' - ' . implode(' | ', $priceParts) : '';
                                @endphp
                                <option value="{{ $dahabia->id }}" data-price-per-person="{{ $dahabia->price_per_person }}" data-price-per-charter="{{ $dahabia->price_per_charter }}" data-currency="{{ $dahabia->currency }}">
                                    {{ $dahabia->name }}@if($dahabia->city) ({{ $dahabia->city->name }})@endif{!! $priceText !!}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addDahabiaBtn" >
                                <i class="fas fa-plus me-1"></i>Add Dahabia
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="dahabia_from_date" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="dahabia_from_date" />
                    </div>
                    <div class="col-md-3">
                        <label for="dahabia_from_time" class="form-label">From Time</label>
                        <input type="time" class="form-control" id="dahabia_from_time" />
                    </div>
                    <div class="col-md-3">
                        <label for="dahabia_to_date" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="dahabia_to_date" />
                    </div>
                    <div class="col-md-3">
                        <label for="dahabia_to_time" class="form-label">To Time</label>
                        <input type="time" class="form-control" id="dahabia_to_time" />
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="dahabia_price_type" class="form-label">Price Type</label>
                        <select id="dahabia_price_type" class="form-select">
                            <option value="person" selected>Per Person</option>
                            <option value="charter">Per Charter</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="dahabia_new_price" class="form-label">New Price</label>
                        <div class="input-group">
                            <span class="input-group-text" id="dahabia_currency_badge_new">$</span>
                            <input type="number" step="0.5" class="form-control w-22px" id="dahabia_new_price" placeholder="Enter new price" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="dahabia_increase_percent" class="form-label">Increase By (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" class="form-control w-22px" id="dahabia_increase_percent" placeholder="e.g. 10" />
                            <button type="button" class="btn btn-outline-secondary" id="dahabia_increase_btn">Increase Price</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Restaurant Tab -->
            @if($showRestaurants)
            <div class="tab-pane fade {{ $activeTabSet && $firstTabId === 'restaurant' ? 'show active' : '' }}" id="restaurant" role="tabpanel" aria-labelledby="restaurant-tab">

                <div class="row">
                    <div class="col-md-8">
                        <label for="restaurant_select" class="form-label">Select Restaurant</label>
                        <select class="form-select" id="restaurant_select" name="restaurant_id">
                            <option value="">Choose a restaurant...</option>
                            @foreach($availableResources['restaurants'] as $restaurant)
                                <option value="{{ $restaurant->id }}" data-meals="{{ $restaurant->meals->toJson() }}"> 
                                    {{ $restaurant->name }}@if($restaurant->city) ({{ $restaurant->city->name }})@endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-primary w-100" id="addRestaurantBtn" disabled>
                                <i class="fas fa-plus me-1"></i>Add Restaurant
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Meals Selection Section -->
                <div class="row mt-3" id="meals-selection-section" style="display: none;">
                    <div class="col-md-12">
                        <label class="form-label">Select Meals</label>
                        <div class="border p-3 rounded bg-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Available Meals</label>
                                    <select id="available-meals" class="form-select" size="6" multiple>
                                        <!-- Meals will be populated dynamically -->
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Selected Meals</label>
                                    <select id="selected-meals" class="form-select" size="6" multiple>
                                        <!-- Selected meals will be shown here -->
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-meal-btn">
                                        <i class="fas fa-arrow-right"></i> Add Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="remove-meal-btn">
                                        <i class="fas fa-arrow-left"></i> Remove Selected
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="clear-meals-btn">
                                        <i class="fas fa-times"></i> Clear All
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Hold Ctrl/Cmd to select multiple meals
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-3">
                        <label for="restaurant_date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="restaurant_date" />
                    </div>
                    <div class="col-md-3">
                        <label for="restaurant_time" class="form-label">Time</label>
                        <input type="time" class="form-control" id="restaurant_time" />
                    </div>
                    <div class="col-md-3">
                        <label for="restaurant_seats" class="form-label">Number of Seats</label>
                        <input type="number" min="1" step="1" class="form-control" id="restaurant_seats" placeholder="e.g. 4" />
                    </div>
                </div>

            </div>
            @endif
        </div>
        @endif

        <!-- Separator -->
        <hr class="my-4">

        <!-- Enhanced Resources Table -->
        <div class="table-responsive">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="fas fa-list me-2"></i>Current Resources
                    @if($inquiry->resources->count() > 0)
                        <span class="badge bg-info">{{ $inquiry->resources->count() }} items</span>
                    @endif
                </h6>
                @if($inquiry->resources->count() > 0)
                    <div class="btn-group btn-group-sm" role="group">
                        
                        <button type="button" class="btn btn-outline-primary" id="calculateTotalBtn">
                            <i class="fas fa-calculator me-1"></i>Calculate Total
                        </button>
                    </div>
                @endif
            </div>
            
            @if(!Gate::allows('manage-inquiry-resources'))
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>View Only:</strong> This shows resources that have been assigned to this inquiry by Operations staff. 
                    You can see which resources are available and who added them.
                </div>
            @endif
            
            <table class="table table-striped" id="resourcesTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Resource Name</th>
                        <th class="resource-details-col" style="display: none;">Details</th>
                        <th>Pricing</th>
                        <th>Duration/Dates</th>
                        <th>Added By</th>
                        <th>Added At</th>
                        @if(Gate::allows('manage-inquiry-resources'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($inquiry->resources as $resource)
                        <tr data-resource-id="{{ $resource->id }}" class="resource-row">
                            <td>
                                @php
                                    $badgeColor = match($resource->resource_type) {
                                        'hotel' => 'bg-primary',
                                        'vehicle' => 'bg-success',
                                        'guide' => 'bg-info',
                                        'representative' => 'bg-warning',
                                        'extra' => 'bg-secondary',
                                        'ticket' => 'bg-purple',
                                        'dahabia' => 'bg-cyan',
                                        'restaurant' => 'bg-orange',
                                        default => 'bg-secondary'
                                    };
                                    
                                    $icon = match($resource->resource_type) {
                                        'hotel' => 'fas fa-hotel',
                                        'vehicle' => 'fas fa-car',
                                        'guide' => 'fas fa-user-tie',
                                        'representative' => 'fas fa-handshake',
                                        'extra' => 'fas fa-plus-circle',
                                        'ticket' => 'fas fa-ticket-alt',
                                        'dahabia' => 'fas fa-sailboat',
                                        'restaurant' => 'fas fa-utensils',
                                        default => 'fas fa-question-circle'
                                    };
                                @endphp
                                <span class="badge {{ $badgeColor }}">
                                    <i class="{{ $icon }} me-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $resource->resource_type)) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $resource->resource_name }}</div>
                                @if($resource->resource_details && isset($resource->resource_details['city']))
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $resource->resource_details['city'] }}
                                    </small>
                                @endif
                                @if($resource->resource_type === 'hotel' && ($resource->number_of_rooms || $resource->number_of_adults || $resource->number_of_children))
                                    <div class="small text-muted mt-1">
                                        @if($resource->number_of_rooms)
                                            <span class="me-2"><i class="fas fa-bed me-1"></i>{{ $resource->number_of_rooms }} room(s)</span>
                                        @endif
                                        @if($resource->number_of_adults)
                                            <span class="me-2"><i class="fas fa-user me-1"></i>{{ $resource->number_of_adults }} adult(s)</span>
                                        @endif
                                        @if($resource->number_of_children)
                                            <span><i class="fas fa-child me-1"></i>{{ $resource->number_of_children }} child(ren)</span>
                                        @endif
                                    </div>
                                @elseif($resource->resource_type === 'guide' && $resource->resource_details && isset($resource->resource_details['selected_languages']))
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-language me-1"></i>
                                        <strong>{{ count($resource->resource_details['selected_languages']) }} language(s)</strong>
                                    </div>
                                    <div class="small mt-1">
                                        @foreach($resource->resource_details['selected_languages'] as $index => $language)
                                            @if($index < 3)
                                                <span class="badge bg-info text-white me-1 mb-1">
                                                    {{ ucfirst($language) }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if(count($resource->resource_details['selected_languages']) > 3)
                                            <span class="badge bg-secondary">+{{ count($resource->resource_details['selected_languages']) - 3 }} more</span>
                                        @endif
                                    </div>
                                    @if($resource->resource_details['location'])
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $resource->resource_details['location'] }}
                                        </div>
                                    @endif
                                @elseif($resource->resource_type === 'representative' && $resource->resource_details && isset($resource->resource_details['selected_languages']))
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-language me-1"></i>
                                        <strong>{{ count($resource->resource_details['selected_languages']) }} language(s)</strong>
                                    </div>
                                    <div class="small mt-1">
                                        @foreach($resource->resource_details['selected_languages'] as $index => $language)
                                            @if($index < 3)
                                                <span class="badge bg-warning text-dark me-1 mb-1">
                                                    {{ ucfirst($language) }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if(count($resource->resource_details['selected_languages']) > 3)
                                            <span class="badge bg-secondary">+{{ count($resource->resource_details['selected_languages']) - 3 }} more</span>
                                        @endif
                                    </div>
                                    @if($resource->resource_details['location'])
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $resource->resource_details['location'] }}
                                        </div>
                                    @endif
                                @elseif($resource->resource_type === 'restaurant' && $resource->resource_details && isset($resource->resource_details['selected_meals']))
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-utensils me-1"></i>
                                        <strong>{{ count($resource->resource_details['selected_meals']) }} meal(s) selected</strong>
                                    </div>
                                    <div class="small mt-1">
                                        @foreach($resource->resource_details['selected_meals'] as $index => $meal)
                                            @if($index < 3)
                                                <span class="badge bg-light text-dark me-1 mb-1">
                                                    @if($meal['featured'])⭐ @endif{{ $meal['meal_name'] }}
                                                </span>
                                            @endif
                                        @endforeach
                                        @if(count($resource->resource_details['selected_meals']) > 3)
                                            <span class="badge bg-secondary">+{{ count($resource->resource_details['selected_meals']) - 3 }} more</span>
                                        @endif
                                    </div>
                                @elseif($resource->resource_type === 'extra' && $resource->resource_details && isset($resource->resource_details['is_internal']) && $resource->resource_details['is_internal'])
                                    <div class="small text-muted mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Internal Service</strong>
                                    </div>
                                    @if($resource->resource_details['description'])
                                        <div class="small text-muted mt-1">
                                            {{ Str::limit($resource->resource_details['description'], 100) }}
                                        </div>
                                    @endif
                                    @if($resource->resource_details['price_type'])
                                        <div class="small mt-1">
                                            <span class="badge bg-info text-white">
                                                {{ ucfirst(str_replace('_', ' ', $resource->resource_details['price_type'])) }}
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="resource-details-col" style="display: none;">
                                <button class="btn btn-sm btn-outline-info view-details-btn" 
                                        data-resource-id="{{ $resource->id }}"
                                        title="View detailed information">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </td>
                            <td>
                                @if($resource->effective_price)
                                    <div class="fw-bold text-success">
                                        {{ $resource->currency ?? '$' }} {{ number_format($resource->effective_price, 2) }}
                                        @if($resource->price_type)
                                            <small class="text-muted">/ {{ ucfirst(str_replace('_', ' ', $resource->price_type)) }}</small>
                                        @endif
                                    </div>
                                    @if($resource->total_cost && $resource->total_cost != $resource->effective_price)
                                        <div class="small text-primary">
                                            <strong>Total: {{ $resource->currency ?? '$' }} {{ number_format($resource->total_cost, 2) }}</strong>
                                        </div>
                                    @endif
                                    @if($resource->original_price && $resource->original_price != $resource->effective_price)
                                        <div class="small text-muted">
                                            Original: {{ $resource->currency ?? '$' }} {{ number_format($resource->original_price, 2) }}
                                            @if($resource->increase_percent)
                                                <span class="text-{{ $resource->increase_percent >= 0 ? 'success' : 'danger' }}">
                                                    ({{ $resource->increase_percent > 0 ? '+' : '' }}{{ number_format($resource->increase_percent, 1) }}%)
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted">No pricing set</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $hasStartEnd = $resource->start_at || $resource->end_at;
                                    $hasHotelDates = $resource->check_in || $resource->check_out;
                                @endphp
                                @if($resource->resource_type === 'hotel' && $hasHotelDates)
                                    <div class="small">
                                        <i class="fas fa-calendar-check me-1 text-success"></i>
                                        <strong>Check-in:</strong> {{ $resource->check_in ? $resource->check_in->format('M d, Y') : '—' }}
                                    </div>
                                    <div class="small">
                                        <i class="fas fa-calendar-times me-1 text-danger"></i>
                                        <strong>Check-out:</strong> {{ $resource->check_out ? $resource->check_out->format('M d, Y') : '—' }}
                                    </div>
                                    @if($resource->check_in && $resource->check_out)
                                        <div class="small text-info">
                                            <i class="fas fa-clock me-1"></i>{{ $resource->check_in->diffInDays($resource->check_out) }} night(s)
                                        </div>
                                    @endif
                                @elseif($hasStartEnd)
                                    <div class="small">
                                        <i class="fas fa-play me-1 text-success"></i>
                                        <strong>Start:</strong> {{ $resource->start_at ? $resource->start_at->format('M d, Y H:i') : '—' }}
                                    </div>
                                    <div class="small">
                                        <i class="fas fa-stop me-1 text-danger"></i>
                                        <strong>End:</strong> {{ $resource->end_at ? $resource->end_at->format('M d, Y H:i') : '—' }}
                                    </div>
                                    @if($resource->duration_in_days)
                                        <div class="small text-info">
                                            <i class="fas fa-clock me-1"></i>{{ $resource->duration_in_days }} day(s)
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted small">No dates specified</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title rounded-circle bg-light text-primary">
                                            {{ substr($resource->addedBy->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $resource->addedBy->name }}</div>
                                        @if($resource->addedBy->hasRole(['Operator', 'Admin', 'Administrator']))
                                            <small class="text-success">
                                                <i class="fas fa-user-cog me-1"></i>Operator
                                            </small>
                                        @else
                                            <small class="text-muted">
                                                {{ $resource->addedBy->roles->first()->name ?? 'User' }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small">{{ $resource->created_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $resource->created_at->format('H:i') }}</div>
                                @if($resource->created_at != $resource->updated_at)
                                    <div class="small text-info">
                                        <i class="fas fa-edit me-1"></i>Updated {{ $resource->updated_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>
                            @if(Gate::allows('manage-inquiry-resources'))
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info view-details-btn" 
                                                data-resource-id="{{ $resource->id }}"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger remove-resource" 
                                                data-resource-id="{{ $resource->id }}"
                                                data-resource-name="{{ $resource->resource_name }}"
                                                title="Remove Resource">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr id="no-resources-row">
                            <td colspan="{{ Gate::allows('manage-inquiry-resources') ? '8' : '7' }}" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle me-2"></i>No resources added yet
                                @if(Gate::allows('manage-inquiry-resources'))
                                    <div class="mt-2">
                                        <small>Use the tabs above to add hotels, vehicles, guides, representatives, or extra services to this inquiry.</small>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($inquiry->resources->count() > 0)
                <tfoot>
                    <tr class="table-info">
                        <td colspan="{{ Gate::allows('manage-inquiry-resources') ? '7' : '6' }}" class="text-end fw-bold">
                            <span id="total-resources">Total Resources: {{ $inquiry->resources->count() }}</span>
                        </td>
                        @if(Gate::allows('manage-inquiry-resources'))
                            <td class="text-center fw-bold" id="total-cost">
                                <span class="text-primary">Total Cost: <span id="calculated-total">Calculate</span></span>
                            </td>
                        @endif
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- Resource Details Modal -->
<div class="modal fade" id="resourceDetailsModal" tabindex="-1" aria-labelledby="resourceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resourceDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Resource Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="resourceDetailsContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading resource details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.nav-tabs .nav-link {
    border: 1px solid transparent;
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.15s ease-in-out;
}

.nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs .nav-link i {
    margin-right: 0.5rem;
}

.tab-content {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    padding: 1.5rem;
    background-color: #fff;
}

.form-select:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.alert {
    border-radius: 0.375rem;
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.avatar-sm {
    width: 32px;
    height: 32px;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.resource-row:hover {
    background-color: #f8f9fa;
}

.btn-group-sm > .btn, .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

#resourcesTable tfoot td {
    background-color: #e3f2fd;
    font-weight: 600;
}
</style>
@endpush

@if(Gate::allows('manage-inquiry-resources'))
<script>
// Wait for jQuery to be available
function waitForJQuery() {
    if (typeof $ !== 'undefined') {
        console.log('=== IMPROVED RESOURCES SCRIPT LOADED ===');
        console.log('jQuery version:', $.fn.jquery);
        
        $(document).ready(function() {
            const inquiryId = {{ $inquiry->id }};
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            console.log('Inquiry ID:', inquiryId);
            console.log('CSRF Token:', csrfToken);
            
            // Toggle details column
            $('#toggleDetailsBtn').on('click', function() {
                $('.resource-details-col').toggle();
                const isVisible = $('.resource-details-col').is(':visible');
                $(this).find('i').removeClass('fa-eye fa-eye-slash').addClass(isVisible ? 'fa-eye-slash' : 'fa-eye');
                $(this).find('span').text(isVisible ? 'Hide Details' : 'Show Details');
            });

            // Calculate total cost
            $('#calculateTotalBtn').on('click', function() {
                let totalCost = 0;
                let currency = '$';
                let hasValidPricing = false;

                $('.resource-row').each(function() {
                    const resourceId = $(this).data('resource-id');
                    // This would need to be enhanced to actually calculate from the resource data
                    // For now, we'll show a placeholder
                });

                $('#calculated-total').text('$0.00 (Feature in development)');
                showAlert('info', 'Total cost calculation is being enhanced. This will show accurate totals based on durations and pricing.');
            });

            // View resource details
            $(document).on('click', '.view-details-btn', function() {
                const resourceId = $(this).data('resource-id');
                showResourceDetails(resourceId);
            });

            // Handle resource selection changes - Direct approach
            $('#hotel_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addHotelBtn').prop('disabled', !resourceId);
                console.log('Hotel selected:', resourceId, 'Button disabled:', $('#addHotelBtn').prop('disabled'));
                const selected = $(this).find('option:selected');
                const currency = selected.data('currency') || '$';
                $('#hotel_currency_badge_new').text(currency);
                $('#hotel_currency_badge_adult').text(currency);
                $('#hotel_currency_badge_child').text(currency);
            });
            
            $('#vehicle_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addVehicleBtn').prop('disabled', !resourceId);
                console.log('Vehicle selected:', resourceId, 'Button disabled:', $('#addVehicleBtn').prop('disabled'));

                // Populate original price & currency from selected option
                const selected = $(this).find('option:selected');
                const priceType = $('#vehicle_price_type').val();
                const currency = selected.data('currency') || '$';
                const original = priceType === 'hour' ? selected.data('price-per-hour') : selected.data('price-per-day');
                $('#vehicle_currency_badge_new').text(currency || '$');
            });

            // Update currency badge when price type changes (no original price input)
            $('#vehicle_price_type').on('change', function() {
                const selected = $('#vehicle_select').find('option:selected');
                const currency = selected.data('currency') || '$';
                $('#vehicle_currency_badge_new').text(currency || '$');
            });
            
            $('#guide_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addGuideBtn').prop('disabled', !resourceId);
                console.log('Guide selected:', resourceId, 'Button disabled:', $('#addGuideBtn').prop('disabled'));
            });

            // Guide currency change handler
            $('#guide_currency').on('change', function() {
                const selectedCurrency = $(this).val();
                const currencySymbols = {
                    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥', 'CAD': 'C$', 'AUD': 'A$',
                    'CHF': 'CHF', 'CNY': '¥', 'INR': '₹', 'AED': 'د.إ', 'EGP': 'EGP'
                };
                const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
                $('#guide_currency_badge').text(symbol);
            });

            // Language selection functionality
            $('#add-language-btn').on('click', function() {
                const selectedLanguages = $('#available-languages option:selected');
                selectedLanguages.each(function() {
                    const languageOption = $(this);
                    const languageValue = languageOption.val();
                    
                    // Check if language is already selected
                    if ($('#selected-languages option[value="' + languageValue + '"]').length === 0) {
                        $('#selected-languages').append(languageOption.clone());
                        languageOption.remove();
                    }
                });
            });
            
            $('#remove-language-btn').on('click', function() {
                const selectedLanguages = $('#selected-languages option:selected');
                selectedLanguages.each(function() {
                    const languageOption = $(this);
                    const languageValue = languageOption.val();
                    
                    // Add back to available languages
                    $('#available-languages').append(languageOption.clone());
                    languageOption.remove();
                });
            });
            
            $('#clear-languages-btn').on('click', function() {
                // Move all selected languages back to available
                $('#selected-languages option').each(function() {
                    $('#available-languages').append($(this).clone());
                });
                $('#selected-languages').empty();
            });
            
            // Double-click to move languages
            $('#available-languages').on('dblclick', 'option', function() {
                const languageOption = $(this);
                const languageValue = languageOption.val();
                
                if ($('#selected-languages option[value="' + languageValue + '"]').length === 0) {
                    $('#selected-languages').append(languageOption.clone());
                    languageOption.remove();
                }
            });
            
            $('#selected-languages').on('dblclick', 'option', function() {
                const languageOption = $(this);
                const languageValue = languageOption.val();
                
                $('#available-languages').append(languageOption.clone());
                languageOption.remove();
            });
            
            $('#representative_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addRepresentativeBtn').prop('disabled', !resourceId);
                console.log('Representative selected:', resourceId, 'Button disabled:', $('#addRepresentativeBtn').prop('disabled'));
            });

            // Representative currency change handler
            $('#representative_currency').on('change', function() {
                const selectedCurrency = $(this).val();
                const currencySymbols = {
                    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥', 'CAD': 'C$', 'AUD': 'A$',
                    'CHF': 'CHF', 'CNY': '¥', 'INR': '₹', 'AED': 'د.إ', 'EGP': 'EGP'
                };
                const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
                $('#representative_currency_badge').text(symbol);
            });

            // Representative language selection functionality
            $('#add-representative-language-btn').on('click', function() {
                const selectedLanguages = $('#available-representative-languages option:selected');
                selectedLanguages.each(function() {
                    const languageOption = $(this);
                    const languageValue = languageOption.val();
                    
                    // Check if language is already selected
                    if ($('#selected-representative-languages option[value="' + languageValue + '"]').length === 0) {
                        $('#selected-representative-languages').append(languageOption.clone());
                        languageOption.remove();
                    }
                });
            });
            
            $('#remove-representative-language-btn').on('click', function() {
                const selectedLanguages = $('#selected-representative-languages option:selected');
                selectedLanguages.each(function() {
                    const languageOption = $(this);
                    const languageValue = languageOption.val();
                    
                    // Add back to available languages
                    $('#available-representative-languages').append(languageOption.clone());
                    languageOption.remove();
                });
            });
            
            $('#clear-representative-languages-btn').on('click', function() {
                // Move all selected languages back to available
                $('#selected-representative-languages option').each(function() {
                    $('#available-representative-languages').append($(this).clone());
                });
                $('#selected-representative-languages').empty();
            });
            
            // Double-click to move representative languages
            $('#available-representative-languages').on('dblclick', 'option', function() {
                const languageOption = $(this);
                const languageValue = languageOption.val();
                
                if ($('#selected-representative-languages option[value="' + languageValue + '"]').length === 0) {
                    $('#selected-representative-languages').append(languageOption.clone());
                    languageOption.remove();
                }
            });
            
            $('#selected-representative-languages').on('dblclick', 'option', function() {
                const languageOption = $(this);
                const languageValue = languageOption.val();
                
                $('#available-representative-languages').append(languageOption.clone());
                languageOption.remove();
            });
            
            // Extra service form validation
            function validateExtraServiceForm() {
                const serviceName = $('#extra_service_name').val().trim();
                const description = $('#extra_service_description').val().trim();
                const price = $('#extra_service_price').val();
                
                const isValid = serviceName && description && price && parseFloat(price) > 0;
                $('#addExtraBtn').prop('disabled', !isValid);
                return isValid;
            }
            
            // Validate form on input changes
            $('#extra_service_name, #extra_service_description, #extra_service_price').on('input', validateExtraServiceForm);
            
            // Extra service currency change handler
            $('#extra_service_currency').on('change', function() {
                const selectedCurrency = $(this).val();
                const currencySymbols = {
                    'USD': '$', 'EUR': '€', 'GBP': '£', 'JPY': '¥', 'CAD': 'C$', 'AUD': 'A$',
                    'CHF': 'CHF', 'CNY': '¥', 'INR': '₹', 'AED': 'د.إ', 'EGP': 'EGP'
                };
                const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
                $('#extra_currency_badge').text(symbol);
            });
            
            $('#ticket_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addTicketBtn').prop('disabled', !resourceId);
                console.log('Ticket selected:', resourceId, 'Button disabled:', $('#addTicketBtn').prop('disabled'));
                const selected = $(this).find('option:selected');
                const currency = selected.data('currency') || '$';
                $('#ticket_currency_badge_new').text(currency);
            });
            
            
            $('#dahabia_select').on('change', function() {
                const resourceId = $(this).val();
                $('#addDahabiaBtn').prop('disabled', !resourceId);
                console.log('Dahabia selected:', resourceId, 'Button disabled:', $('#addDahabiaBtn').prop('disabled'));
                const selected = $(this).find('option:selected');
                const currency = selected.data('currency') || '$';
                $('#dahabia_currency_badge_new').text(currency);
            });
            
            $('#restaurant_select').on('change', function() {
                const resourceId = $(this).val();
                const selectedOption = $(this).find('option:selected');
                const mealsData = selectedOption.data('meals');
                
                // Clear previous meals
                $('#available-meals').empty();
                $('#selected-meals').empty();
                
                if (resourceId && mealsData && mealsData.length > 0) {
                    // Show meals selection section
                    $('#meals-selection-section').show();
                    
                    // Populate available meals
                    mealsData.forEach(function(meal) {
                        const option = $('<option></option>')
                            .attr('value', meal.id)
                            .attr('data-price', meal.price)
                            .attr('data-currency', meal.currency)
                            .attr('data-featured', meal.is_featured)
                            .text(meal.name + ' - ' + meal.currency + ' ' + parseFloat(meal.price).toFixed(2));
                        
                        if (meal.is_featured) {
                            option.prepend('⭐ ');
                        }
                        
                        $('#available-meals').append(option);
                    });
                    
                    // Enable add button only if meals are selected
                    $('#addRestaurantBtn').prop('disabled', true);
                } else {
                    // Hide meals selection section
                    $('#meals-selection-section').hide();
                $('#addRestaurantBtn').prop('disabled', !resourceId);
                }
                
                console.log('Restaurant selected:', resourceId, 'Meals count:', mealsData ? mealsData.length : 0);
            });
            
            // Meal selection functionality
            $('#add-meal-btn').on('click', function() {
                const selectedMeals = $('#available-meals option:selected');
                selectedMeals.each(function() {
                    const mealOption = $(this);
                    const mealId = mealOption.val();
                    const mealText = mealOption.text();
                    
                    // Check if meal is already selected
                    if ($('#selected-meals option[value="' + mealId + '"]').length === 0) {
                        $('#selected-meals').append(mealOption.clone());
                        mealOption.remove();
                    }
                });
                
                // Enable add restaurant button if meals are selected
                if ($('#selected-meals option').length > 0) {
                    $('#addRestaurantBtn').prop('disabled', false);
                }
            });
            
            $('#remove-meal-btn').on('click', function() {
                const selectedMeals = $('#selected-meals option:selected');
                selectedMeals.each(function() {
                    const mealOption = $(this);
                    const mealId = mealOption.val();
                    const mealText = mealOption.text();
                    
                    // Add back to available meals
                    $('#available-meals').append(mealOption.clone());
                    mealOption.remove();
                });
                
                // Disable add restaurant button if no meals selected
                if ($('#selected-meals option').length === 0) {
                    $('#addRestaurantBtn').prop('disabled', true);
                }
            });
            
            $('#clear-meals-btn').on('click', function() {
                // Move all selected meals back to available
                $('#selected-meals option').each(function() {
                    $('#available-meals').append($(this).clone());
                });
                $('#selected-meals').empty();
                $('#addRestaurantBtn').prop('disabled', true);
            });
            
            // Double-click to move meals
            $('#available-meals').on('dblclick', 'option', function() {
                const mealOption = $(this);
                const mealId = mealOption.val();
                
                if ($('#selected-meals option[value="' + mealId + '"]').length === 0) {
                    $('#selected-meals').append(mealOption.clone());
                    mealOption.remove();
                    
                    if ($('#selected-meals option').length > 0) {
                        $('#addRestaurantBtn').prop('disabled', false);
                    }
                }
            });
            
            $('#selected-meals').on('dblclick', 'option', function() {
                const mealOption = $(this);
                const mealId = mealOption.val();
                
                $('#available-meals').append(mealOption.clone());
                mealOption.remove();
                
                if ($('#selected-meals option').length === 0) {
                    $('#addRestaurantBtn').prop('disabled', true);
                }
            });
            
            // Handle add resource button clicks
            $('#addHotelBtn').on('click', function() {
                addResource('hotel');
            });
            
            $('#addVehicleBtn').on('click', function() {
                addResource('vehicle');
            });

            // Increase price by percentage button (compute from DB price via selected option)
            $('#vehicle_increase_btn').on('click', function() {
                const selected = $('#vehicle_select').find('option:selected');
                const priceType = $('#vehicle_price_type').val();
                const percentStr = $('#vehicle_increase_percent').val();
                const original = priceType === 'hour' ? parseFloat(selected.data('price-per-hour')) : parseFloat(selected.data('price-per-day'));
                const percent = parseFloat(percentStr);
                if (!selected.val() || isNaN(original)) {
                    showAlert('error', 'Select a vehicle with a valid price first.');
                    return;
                }
                if (isNaN(percent)) {
                    showAlert('error', 'Please enter a valid percentage to increase/decrease.');
                    return;
                }
                const increased = original * (1 + percent / 100);
                $('#vehicle_new_price').val(increased.toFixed(2));
            });
            
            $('#addGuideBtn').on('click', function() {
                addResource('guide');
            });
            
            $('#addRepresentativeBtn').on('click', function() {
                addResource('representative');
            });
            
            $('#addExtraBtn').on('click', function() {
                addResource('extra');
            });
            
            $('#addTicketBtn').on('click', function() {
                addResource('ticket');
            });
            
            
            $('#addDahabiaBtn').on('click', function() {
                addResource('dahabia');
            });
            
            $('#addRestaurantBtn').on('click', function() {
                addResource('restaurant');
            });
            
            // Increase price by percentage button (compute from DB price via selected option)
            $('#hotel_increase_btn').on('click', function() {
                const selected = $('#hotel_select').find('option:selected');
                const original = parseFloat(selected.data('price-per-night'));
                const percent = parseFloat($('#hotel_increase_percent').val());
                if (!selected.val() || isNaN(original)) {
                    showAlert('error', 'Select a hotel with a valid price first.');
                    return;
                }
                if (isNaN(percent)) {
                    showAlert('error', 'Please enter a valid percentage to increase/decrease.');
                    return;
                }
                const increased = original * (1 + percent / 100);
                $('#hotel_new_price').val(increased.toFixed(2));
            });
            
            $('#ticket_increase_btn').on('click', function() {
                const selected = $('#ticket_select').find('option:selected');
                const original = parseFloat(selected.data('price-per-person'));
                const percent = parseFloat($('#ticket_increase_percent').val());
                if (!selected.val() || isNaN(original)) {
                    showAlert('error', 'Select a ticket with a valid price first.');
                    return;
                }
                if (isNaN(percent)) {
                    showAlert('error', 'Please enter a valid percentage to increase/decrease.');
                    return;
                }
                const increased = original * (1 + percent / 100);
                $('#ticket_new_price').val(increased.toFixed(2));
            });
            
            
            $('#dahabia_increase_btn').on('click', function() {
                const selected = $('#dahabia_select').find('option:selected');
                const priceType = $('#dahabia_price_type').val();
                const percent = parseFloat($('#dahabia_increase_percent').val());
                const original = priceType === 'charter' ? parseFloat(selected.data('price-per-charter')) : parseFloat(selected.data('price-per-person'));
                if (!selected.val() || isNaN(original)) {
                    showAlert('error', 'Select a dahabia with a valid price first.');
                    return;
                }
                if (isNaN(percent)) {
                    showAlert('error', 'Please enter a valid percentage to increase/decrease.');
                    return;
                }
                const increased = original * (1 + percent / 100);
                $('#dahabia_new_price').val(increased.toFixed(2));
            });
            
            
            // Function to add a resource
            function addResource(resourceType) {
                const buttonId = '#add' + resourceType.charAt(0).toUpperCase() + resourceType.slice(1) + 'Btn';
                const button = $(buttonId);
                
                // For internal extra services, we don't need resource_id validation
                if (resourceType !== 'extra') {
                    const selectId = '#' + resourceType + '_select';
                    const select = $(selectId);
                    const resourceId = select.val();
                    
                    if (!resourceId) {
                        showAlert('error', 'Please select a ' + resourceType + ' first');
                        return;
                    }
                }
                
                const formData = {
                    resource_type: resourceType,
                    _token: csrfToken
                };
                
                // Add bypass duplicate check if checkbox is checked
                const allowMultipleCheckbox = $('#' + resourceType + '_allow_multiple');
                if (allowMultipleCheckbox.length && allowMultipleCheckbox.is(':checked')) {
                    formData.bypass_duplicate_check = true;
                }
                
                // Add resource_id only for external resources
                if (resourceType !== 'extra') {
                    const selectId = '#' + resourceType + '_select';
                    const select = $(selectId);
                    formData.resource_id = select.val();
                }
                
                if (resourceType === 'hotel') {
                    formData.check_in = $('#hotel_check_in').val();
                    formData.check_out = $('#hotel_check_out').val();
                    formData.number_of_rooms = $('#hotel_number_of_rooms').val();
                    formData.number_of_adults = $('#hotel_number_of_adults').val();
                    formData.number_of_children = $('#hotel_number_of_children').val();
                    formData.rate_per_adult = $('#hotel_rate_per_adult').val();
                    formData.rate_per_child = $('#hotel_rate_per_child').val();
                    formData.price_type = $('#hotel_price_type').val();
                    const selected = $('#hotel_select').find('option:selected');
                    const currency = selected.data('currency');
                    const newPrice = $('#hotel_new_price').val();
                    const increasePercent = $('#hotel_increase_percent').val();
                    if (currency) formData.currency = currency;
                    if (newPrice) formData.new_price = newPrice;
                    if (increasePercent) formData.increase_percent = increasePercent;
                } else if (resourceType === 'vehicle') {
                    formData.start_date = $('#vehicle_from_date').val();
                    formData.start_time = $('#vehicle_from_time').val();
                    formData.end_time = $('#vehicle_to_time').val();
                    formData.price_type = $('#vehicle_price_type').val();
                    const selected = $('#vehicle_select').find('option:selected');
                    const currency = selected.data('currency');
                    const newPrice = $('#vehicle_new_price').val();
                    const increasePercent = $('#vehicle_increase_percent').val();
                    if (currency) formData.currency = currency;
                    if (newPrice) formData.new_price = newPrice;
                    if (increasePercent) formData.increase_percent = increasePercent;
                } else if (resourceType === 'ticket') {
                    formData.start_date = $('#ticket_date').val();
                    formData.start_time = $('#ticket_time').val();
                    formData.number_of_rooms = $('#ticket_quantity').val(); // Repurpose field for quantity
                    formData.price_type = $('#ticket_price_type').val();
                    const selected = $('#ticket_select').find('option:selected');
                    const currency = selected.data('currency');
                    const newPrice = $('#ticket_new_price').val();
                    const increasePercent = $('#ticket_increase_percent').val();
                    if (currency) formData.currency = currency;
                    if (newPrice) formData.new_price = newPrice;
                    if (increasePercent) formData.increase_percent = increasePercent;
                } else if (resourceType === 'dahabia') {
                        formData.start_date = $('#dahabia_from_date').val();
                        formData.start_time = $('#dahabia_from_time').val();
                        formData.end_date = $('#dahabia_to_date').val();
                        formData.end_time = $('#dahabia_to_time').val();
                        formData.price_type = $('#dahabia_price_type').val();
                        const selected = $('#dahabia_select').find('option:selected');
                        const currency = selected.data('currency');
                        const newPrice = $('#dahabia_new_price').val();
                        const increasePercent = $('#dahabia_increase_percent').val();
                        if (currency) formData.currency = currency;
                        if (newPrice) formData.new_price = newPrice;
                        if (increasePercent) formData.increase_percent = increasePercent;
                } else if (resourceType === 'guide') {
                    formData.start_date = $('#guide_start_date').val();
                    formData.start_time = $('#guide_start_time').val();
                    formData.end_time = $('#guide_end_time').val();
                    formData.price_type = $('#guide_price_type').val();
                    formData.new_price = $('#guide_price').val();
                    formData.currency = $('#guide_currency').val();
                    formData.location = $('#guide_location').val();
                    formData.description = $('#guide_description').val();
                    
                    // Get selected languages
                    const selectedLanguages = [];
                    $('#selected-languages option').each(function() {
                        selectedLanguages.push($(this).val());
                    });
                    
                    if (selectedLanguages.length === 0) {
                        showAlert('error', 'Please select at least one language');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                    
                    formData.selected_languages = selectedLanguages;
                } else if (resourceType === 'representative') {
                    formData.start_date = $('#representative_start_date').val();
                    formData.start_time = $('#representative_start_time').val();
                    formData.end_time = $('#representative_end_time').val();
                    formData.price_type = $('#representative_price_type').val();
                    formData.new_price = $('#representative_price').val();
                    formData.currency = $('#representative_currency').val();
                    formData.location = $('#representative_location').val();
                    formData.description = $('#representative_description').val();
                    
                    // Get selected languages
                    const selectedLanguages = [];
                    $('#selected-representative-languages option').each(function() {
                        selectedLanguages.push($(this).val());
                    });
                    
                    if (selectedLanguages.length === 0) {
                        showAlert('error', 'Please select at least one language');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                    
                    formData.selected_languages = selectedLanguages;
                } else if (resourceType === 'restaurant') {
                    formData.start_date = $('#restaurant_date').val();
                    formData.start_time = $('#restaurant_time').val();
                    formData.number_of_rooms = $('#restaurant_seats').val(); // Repurpose field for seats
                    
                    // Get selected meals
                    const selectedMeals = [];
                    $('#selected-meals option').each(function() {
                        const mealOption = $(this);
                        selectedMeals.push({
                            id: mealOption.val(),
                            name: mealOption.text().replace(/⭐\s*/, ''), // Remove star emoji
                            price: mealOption.data('price'),
                            currency: mealOption.data('currency'),
                            featured: mealOption.data('featured')
                        });
                    });
                    
                    if (selectedMeals.length === 0) {
                        showAlert('error', 'Please select at least one meal');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                    
                    formData.selected_meals = selectedMeals;
                } else if (resourceType === 'extra') {
                    // Internal extra service creation
                    formData.service_name = $('#extra_service_name').val().trim();
                    formData.description = $('#extra_service_description').val().trim();
                    formData.price = $('#extra_service_price').val();
                    formData.currency = $('#extra_service_currency').val();
                    formData.price_type = $('#extra_service_price_type').val();
                    
                    // Validate required fields
                    if (!formData.service_name) {
                        showAlert('error', 'Please enter a service name');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                    
                    if (!formData.description) {
                        showAlert('error', 'Please enter a service description');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                    
                    if (!formData.price || parseFloat(formData.price) <= 0) {
                        showAlert('error', 'Please enter a valid price');
                        button.prop('disabled', false).html(originalText);
                        return;
                    }
                }
                
                const originalText = button.html();
                
                // Show loading state
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
                
                $.ajax({
                    url: `/dashboard/inquiries/${inquiryId}/resources`,
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', response.message);
                            
                            // Clear extra service form if it was an extra service
                            if (resourceType === 'extra') {
                                $('#extra_service_name').val('');
                                $('#extra_service_description').val('');
                                $('#extra_service_price').val('');
                                $('#extra_service_currency').val('USD');
                                $('#extra_service_price_type').val('per_person');
                                $('#extra_currency_badge').text('$');
                                validateExtraServiceForm();
                            }
                            
                            // Reload the page to show the updated resources table
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showAlert('error', response.message || 'Failed to add resource');
                        }
                    },
                    error: function(xhr, status, error) {
                        let message = 'Failed to add resource';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    message = response.message;
                                }
                            } catch (e) {
                                // Could not parse response as JSON
                            }
                        }
                        showAlert('error', message);
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalText);
                    }
                });
            }
            
            // Handle remove resource
            $(document).on('click', '.remove-resource', function() {
                const resourceId = $(this).data('resource-id');
                const resourceName = $(this).data('resource-name');
                
                if (confirm(`Are you sure you want to remove "${resourceName}" from this inquiry?`)) {
                    const removeBtn = $(this);
                    const originalHtml = removeBtn.html();
                    
                    // Show loading state
                    removeBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                    
                    $.ajax({
                        url: `/dashboard/inquiries/resources/${resourceId}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        success: function(response) {
                            if (response.success) {
                                showAlert('success', response.message);
                                
                                // Remove row from table
                                $(`tr[data-resource-id="${resourceId}"]`).fadeOut(300, function() {
                                    $(this).remove();
                                    
                                    // Show empty message if no resources left
                                    if ($('#resourcesTable tbody tr:visible').length === 0) {
                                        const colSpan = $('#resourcesTable thead tr th').length;
                                        $('#resourcesTable tbody').html(`
                                            <tr id="no-resources-row">
                                                <td colspan="${colSpan}" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle me-2"></i>No resources added yet
                                                    <div class="mt-2">
                                                        <small>Use the tabs above to add hotels, vehicles, guides, representatives, or extra services to this inquiry.</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        `);
                                    }
                                    
                                    // Update total count
                                    const remainingCount = $('#resourcesTable tbody tr:visible').length;
                                    $('#total-resources').text(`Total Resources: ${remainingCount}`);
                                });
                            } else {
                                showAlert('error', response.message || 'Failed to remove resource');
                                removeBtn.prop('disabled', false).html(originalHtml);
                            }
                        },
                        error: function(xhr) {
                            let message = 'Failed to remove resource';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showAlert('error', message);
                            removeBtn.prop('disabled', false).html(originalHtml);
                        }
                    });
                }
            });

            // Function to show resource details
            function showResourceDetails(resourceId) {
                $('#resourceDetailsModal').modal('show');
                $('#resourceDetailsContent').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading resource details...</p>
                    </div>
                `);

                $.ajax({
                    url: `/dashboard/inquiries/resources/${resourceId}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function(response) {
                        if (response.success) {
                            const data = response.data;
                            const details = data.resource_details;
                            
                            let detailsHtml = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-${getResourceIcon(data.resource_type)} me-2"></i>
                                            ${details.name}
                                        </h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Type:</strong></td><td>${data.resource_type.charAt(0).toUpperCase() + data.resource_type.slice(1)}</td></tr>
                                            ${details.city ? `<tr><td><strong>City:</strong></td><td>${details.city}</td></tr>` : ''}
                                            ${details.status ? `<tr><td><strong>Status:</strong></td><td><span class="badge bg-info">${details.status}</span></td></tr>` : ''}
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-secondary mb-3">Assignment Details</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Added By:</strong></td><td>${data.added_by.name}</td></tr>
                                            <tr><td><strong>Added On:</strong></td><td>${data.created_at}</td></tr>
                                            ${data.formatted_price ? `<tr><td><strong>Price:</strong></td><td>${data.formatted_price}</td></tr>` : ''}
                                            ${data.total_cost ? `<tr><td><strong>Total Cost:</strong></td><td>${data.currency || '$'} ${parseFloat(data.total_cost).toFixed(2)}</td></tr>` : ''}
                                        </table>
                                    </div>
                                </div>
                            `;

                            if (details.details && Object.keys(details.details).length > 0) {
                                detailsHtml += `
                                    <hr>
                                    <h6 class="text-info mb-3">Resource Specifications</h6>
                                    <div class="row">
                                `;
                                
                                const detailsArray = Object.entries(details.details).filter(([key, value]) => value !== null && value !== '');
                                const halfLength = Math.ceil(detailsArray.length / 2);
                                
                                detailsHtml += '<div class="col-md-6"><table class="table table-sm">';
                                for (let i = 0; i < halfLength; i++) {
                                    if (detailsArray[i]) {
                                        const [key, value] = detailsArray[i];
                                        const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                        detailsHtml += `<tr><td><strong>${formattedKey}:</strong></td><td>${Array.isArray(value) ? value.join(', ') : value}</td></tr>`;
                                    }
                                }
                                detailsHtml += '</table></div>';
                                
                                if (detailsArray.length > halfLength) {
                                    detailsHtml += '<div class="col-md-6"><table class="table table-sm">';
                                    for (let i = halfLength; i < detailsArray.length; i++) {
                                        const [key, value] = detailsArray[i];
                                        const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                        detailsHtml += `<tr><td><strong>${formattedKey}:</strong></td><td>${Array.isArray(value) ? value.join(', ') : value}</td></tr>`;
                                    }
                                    detailsHtml += '</table></div>';
                                }
                                
                                detailsHtml += '</div>';
                            }

                            // Add booking details if available
                            if (data.start_at || data.end_at || data.check_in || data.check_out) {
                                detailsHtml += `
                                    <hr>
                                    <h6 class="text-success mb-3">Booking Details</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                `;
                                
                                if (data.check_in || data.check_out) {
                                    if (data.check_in) detailsHtml += `<tr><td><strong>Check-in:</strong></td><td>${data.check_in}</td></tr>`;
                                    if (data.check_out) detailsHtml += `<tr><td><strong>Check-out:</strong></td><td>${data.check_out}</td></tr>`;
                                } else {
                                    if (data.start_at) detailsHtml += `<tr><td><strong>Start:</strong></td><td>${data.start_at}</td></tr>`;
                                    if (data.end_at) detailsHtml += `<tr><td><strong>End:</strong></td><td>${data.end_at}</td></tr>`;
                                }
                                
                                if (data.duration_in_days) {
                                    detailsHtml += `<tr><td><strong>Duration:</strong></td><td>${data.duration_in_days} day(s)</td></tr>`;
                                }
                                
                                detailsHtml += '</table></div><div class="col-md-6"><table class="table table-sm">';
                                
                                if (data.number_of_rooms) detailsHtml += `<tr><td><strong>Rooms:</strong></td><td>${data.number_of_rooms}</td></tr>`;
                                if (data.number_of_adults) detailsHtml += `<tr><td><strong>Adults:</strong></td><td>${data.number_of_adults}</td></tr>`;
                                if (data.number_of_children) detailsHtml += `<tr><td><strong>Children:</strong></td><td>${data.number_of_children}</td></tr>`;
                                if (data.rate_per_adult) detailsHtml += `<tr><td><strong>Rate/Adult:</strong></td><td>${data.currency || '$'} ${parseFloat(data.rate_per_adult).toFixed(2)}</td></tr>`;
                                if (data.rate_per_child) detailsHtml += `<tr><td><strong>Rate/Child:</strong></td><td>${data.currency || '$'} ${parseFloat(data.rate_per_child).toFixed(2)}</td></tr>`;
                                
                                detailsHtml += '</table></div></div>';
                            }

                            $('#resourceDetailsContent').html(detailsHtml);
                        } else {
                            $('#resourceDetailsContent').html(`
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Failed to load resource details: ${response.message}
                                </div>
                            `);
                        }
                    },
                    error: function(xhr) {
                        $('#resourceDetailsContent').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Failed to load resource details. Please try again.
                            </div>
                        `);
                    }
                });
            }

            // Helper function to get resource icon
            function getResourceIcon(type) {
                switch(type) {
                    case 'hotel': return 'hotel';
                    case 'vehicle': return 'car';
                    case 'guide': return 'user-tie';
                    case 'representative': return 'handshake';
                    case 'extra': return 'plus-circle';
                    case 'ticket': return 'ticket-alt';
                    case 'dahabia': return 'sailboat';
                    case 'restaurant': return 'utensils';
                    default: return 'question-circle';
                }
            }
            
            // Helper function to show alerts
            function showAlert(type, message) {
                const alertClass = type === 'success' ? 'alert-success' : (type === 'info' ? 'alert-info' : 'alert-danger');
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                // Remove existing alerts
                $('.alert').remove();
                
                // Add new alert
                $('.card-body').prepend(alertHtml);
                
                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    $('.alert').fadeOut();
                }, 5000);
            }
        });
    } else {
        // jQuery not ready yet, try again in 100ms
        setTimeout(waitForJQuery, 100);
    }
}

// Start waiting for jQuery
waitForJQuery();
</script>
@endif
@endif