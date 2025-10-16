@extends('layouts.dashboard.app')

@section('content')
    <div class="page-body">
        <!-- Container-fluid starts-->
        <x-dashboard.partials.breadcrumb title="Edit Restaurant">
            <li class="breadcrumb-item"><a href="{{ route('dashboard.restaurants.index') }}">Restaurants</a></li>
            <li class="breadcrumb-item active">Edit {{ $restaurant->name }}</li>
        </x-dashboard.partials.breadcrumb>
        <!-- Container-fluid Ends-->

        <!-- Container-fluid starts-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <x-dashboard.partials.message-alert />
                    <div class="card">
                        <div class="card-header">
                            <h5>Edit Restaurant</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.restaurants.update', $restaurant) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $restaurant->name) }}" required>
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
                                                    <option value="{{ $city->id }}" {{ old('city_id', $restaurant->city_id) == $city->id ? 'selected' : '' }}>
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
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3">{{ old('description', $restaurant->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="2" placeholder="Restaurant address">{{ old('address', $restaurant->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" placeholder="+1 (555) 123-4567">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $restaurant->email) }}" placeholder="restaurant@example.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                                   id="website" name="website" value="{{ old('website', $restaurant->website) }}" placeholder="https://example.com">
                                            @error('website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price_range" class="form-label">Price Range</label>
                                            <select class="form-control @error('price_range') is-invalid @enderror" 
                                                    id="price_range" name="price_range">
                                                <option value="">Select Price Range</option>
                                                <option value="budget" {{ old('price_range', $restaurant->price_range) == 'budget' ? 'selected' : '' }}>$ - Budget</option>
                                                <option value="moderate" {{ old('price_range', $restaurant->price_range) == 'moderate' ? 'selected' : '' }}>$$ - Moderate</option>
                                                <option value="expensive" {{ old('price_range', $restaurant->price_range) == 'expensive' ? 'selected' : '' }}>$$$ - Expensive</option>
                                                <option value="luxury" {{ old('price_range', $restaurant->price_range) == 'luxury' ? 'selected' : '' }}>$$$$ - Luxury</option>
                                            </select>
                                            @error('price_range')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                            <select class="form-control @error('currency') is-invalid @enderror" 
                                                    id="currency" name="currency" required>
                                                <option value="">Select Currency</option>
                                                @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                                    <option value="{{ $currencyCode }}" {{ old('currency', $restaurant->currency ?: 'EGP') == $currencyCode ? 'selected' : '' }}>
                                                        {{ $currencyCode }} - {{ $currencyName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('currency')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="capacity" class="form-label">Capacity</label>
                                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" name="capacity" value="{{ old('capacity', $restaurant->capacity) }}" min="0" placeholder="Maximum number of guests">
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Meal Information Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="text-primary mb-3">
                                            <i class="fas fa-utensils me-2"></i>Meal Information
                                        </h6>
                                    </div>
                                </div>

                                <div id="meals-container">
                                    @if($restaurant->meals->count() > 0)
                                        @foreach($restaurant->meals as $index => $meal)
                                            <div class="meal-item border p-3 rounded mb-3" data-meal-index="{{ $index }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">Meal Name</label>
                                                            <input type="text" class="form-control meal-name" name="meals[{{ $index }}][name]" value="{{ old('meals.'.$index.'.name', $meal->name) }}" placeholder="e.g., Traditional Egyptian Breakfast">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label">Currency</label>
                                                            <select class="form-control meal-currency" name="meals[{{ $index }}][currency]">
                                                                @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                                                    <option value="{{ $currencyCode }}" {{ old('meals.'.$index.'.currency', $meal->currency ?: 'EGP') == $currencyCode ? 'selected' : '' }}>
                                                                        {{ $currencyCode }} - {{ $currencyName }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label class="form-label">Price</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text meal-currency-symbol">{{ $meal->currency ?: 'EGP' }}</span>
                                                                <input type="number" step="0.01" class="form-control meal-price" name="meals[{{ $index }}][price]" value="{{ old('meals.'.$index.'.price', $meal->price) }}" min="0" placeholder="0.00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label class="form-label">&nbsp;</label>
                                                            <div>
                                                                <button type="button" class="btn btn-danger btn-sm remove-meal">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mb-3">
                                                            <label class="form-label">Description</label>
                                                            <textarea class="form-control meal-description" name="meals[{{ $index }}][description]" rows="2" placeholder="Describe the meal ingredients, preparation, and special features">{{ old('meals.'.$index.'.description', $meal->description) }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input meal-featured" type="checkbox" name="meals[{{ $index }}][is_featured]" value="1" {{ old('meals.'.$index.'.is_featured', $meal->is_featured) ? 'checked' : '' }}>
                                                            <label class="form-check-label">Featured Meal</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-check">
                                                            <input class="form-check-input meal-available" type="checkbox" name="meals[{{ $index }}][is_available]" value="1" {{ old('meals.'.$index.'.is_available', $meal->is_available) ? 'checked' : '' }}>
                                                            <label class="form-check-label">Available</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="meals[{{ $index }}][id]" value="{{ $meal->id }}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="meal-item border p-3 rounded mb-3" data-meal-index="0">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Meal Name</label>
                                                        <input type="text" class="form-control meal-name" name="meals[0][name]" placeholder="e.g., Traditional Egyptian Breakfast">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Currency</label>
                                                        <select class="form-control meal-currency" name="meals[0][currency]">
                                                            @foreach(\App\Services\Dashboard\Currency::getSupportedCurrencies() as $currencyCode => $currencyName)
                                                                <option value="{{ $currencyCode }}" {{ $currencyCode == 'EGP' ? 'selected' : '' }}>
                                                                    {{ $currencyCode }} - {{ $currencyName }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label class="form-label">Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text meal-currency-symbol">EGP</span>
                                                            <input type="number" step="0.01" class="form-control meal-price" name="meals[0][price]" min="0" placeholder="0.00">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label class="form-label">&nbsp;</label>
                                                        <div>
                                                            <button type="button" class="btn btn-danger btn-sm remove-meal" style="display: none;">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Description</label>
                                                        <textarea class="form-control meal-description" name="meals[0][description]" rows="2" placeholder="Describe the meal ingredients, preparation, and special features"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input meal-featured" type="checkbox" name="meals[0][is_featured]" value="1">
                                                        <label class="form-check-label">Featured Meal</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input meal-available" type="checkbox" name="meals[0][is_available]" value="1" checked>
                                                        <label class="form-check-label">Available</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-outline-primary" id="add-meal-btn">
                                            <i class="fas fa-plus me-1"></i>Add Another Meal
                                        </button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="active" name="active" 
                                                   {{ old('active', $restaurant->active) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                                                   {{ old('enabled', $restaurant->enabled) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enabled">
                                                Enabled
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="reservation_required" name="reservation_required" 
                                                   {{ old('reservation_required', $restaurant->reservation_required) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="reservation_required">
                                                Reservation Required
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Restaurant
                                        </button>
                                        <a href="{{ route('dashboard.restaurants.index') }}" class="btn btn-secondary ms-2">
                                            <i class="fas fa-times me-1"></i>Cancel
                                        </a>
                                    </div>
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

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const currencySelect = document.getElementById('currency');
    const mealsContainer = document.getElementById('meals-container');
    const addMealBtn = document.getElementById('add-meal-btn');
    let mealIndex = {{ $restaurant->meals->count() }};
    
    // Currency symbol mapping
    const currencySymbols = {
        'USD': '$',
        'EUR': '€',
        'GBP': '£',
        'JPY': '¥',
        'CAD': 'C$',
        'AUD': 'A$',
        'CHF': 'CHF',
        'CNY': '¥',
        'INR': '₹',
        'AED': 'د.إ',
        'EGP': 'EGP'
    };
    
    // Update currency symbol when currency changes
    currencySelect.addEventListener('change', function() {
        const selectedCurrency = this.value;
        const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
        document.querySelectorAll('.meal-currency-symbol').forEach(symbolEl => {
            symbolEl.textContent = symbol;
        });
    });
    
    // Update currency symbol when meal currency changes
    mealsContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('meal-currency')) {
            const selectedCurrency = e.target.value;
            const symbol = currencySymbols[selectedCurrency] || selectedCurrency;
            const currencySymbol = e.target.closest('.meal-item').querySelector('.meal-currency-symbol');
            if (currencySymbol) {
                currencySymbol.textContent = symbol;
            }
        }
    });
    
    // Set initial currency symbol
    const initialCurrency = currencySelect.value || 'EGP';
    const initialSymbol = currencySymbols[initialCurrency] || initialCurrency;
    document.querySelectorAll('.meal-currency-symbol').forEach(symbolEl => {
        symbolEl.textContent = initialSymbol;
    });
    
    // Add meal functionality
    addMealBtn.addEventListener('click', function() {
        mealIndex++;
        const mealTemplate = `
            <div class="meal-item border p-3 rounded mb-3" data-meal-index="${mealIndex}">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Meal Name</label>
                            <input type="text" class="form-control meal-name" name="meals[${mealIndex}][name]" placeholder="e.g., Traditional Egyptian Breakfast">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Currency</label>
                            <select class="form-control meal-currency" name="meals[${mealIndex}][currency]">
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="AUD">AUD - Australian Dollar</option>
                                <option value="CHF">CHF - Swiss Franc</option>
                                <option value="CNY">CNY - Chinese Yuan</option>
                                <option value="INR">INR - Indian Rupee</option>
                                <option value="AED">AED - UAE Dirham</option>
                                <option value="EGP" selected>EGP - Egyptian Pound</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <div class="input-group">
                                <span class="input-group-text meal-currency-symbol">${initialSymbol}</span>
                                <input type="number" step="0.01" class="form-control meal-price" name="meals[${mealIndex}][price]" min="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-danger btn-sm remove-meal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control meal-description" name="meals[${mealIndex}][description]" rows="2" placeholder="Describe the meal ingredients, preparation, and special features"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input meal-featured" type="checkbox" name="meals[${mealIndex}][is_featured]" value="1">
                            <label class="form-check-label">Featured Meal</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input meal-available" type="checkbox" name="meals[${mealIndex}][is_available]" value="1" checked>
                            <label class="form-check-label">Available</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        mealsContainer.insertAdjacentHTML('beforeend', mealTemplate);
        updateRemoveButtons();
    });
    
    // Remove meal functionality
    mealsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-meal')) {
            e.target.closest('.meal-item').remove();
            updateRemoveButtons();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const mealItems = document.querySelectorAll('.meal-item');
        mealItems.forEach((item, index) => {
            const removeBtn = item.querySelector('.remove-meal');
            removeBtn.style.display = mealItems.length > 1 ? 'inline-block' : 'none';
        });
    }
    
    // Initialize remove buttons
    updateRemoveButtons();
});
</script>
@endpush
