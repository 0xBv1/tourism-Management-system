<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NileCruiseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'name' => 'Nile Cruise Name',
            'description' => 'Description',
            'city_id' => 'City',
            'vessel_type' => 'Vessel Type',
            'capacity' => 'Capacity',
            'price_per_person' => 'Price Per Person',
            'price_per_cabin' => 'Price Per Cabin',
            'currency' => 'Currency',
            'departure_location' => 'Departure Location',
            'arrival_location' => 'Arrival Location',
            'itinerary' => 'Itinerary',
            'meal_plan' => 'Meal Plan',
            'amenities' => 'Amenities',
            'images' => 'Images',
            'status' => 'Status',
            'check_in_time' => 'Check-in Time',
            'check_out_time' => 'Check-out Time',
            'duration_nights' => 'Duration (Nights)',
            'notes' => 'Notes',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city_id' => ['required', 'exists:cities,id'],
            'vessel_type' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_per_person' => ['nullable', 'numeric', 'min:0'],
            'price_per_cabin' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'departure_location' => ['required', 'string', 'max:255'],
            'arrival_location' => ['required', 'string', 'max:255'],
            'itinerary' => ['nullable', 'string'],
            'meal_plan' => ['nullable', 'string', 'max:255'],
            'amenities' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:500'],
            'status' => ['required', Rule::enum(ResourceStatus::class)],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'duration_nights' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'active' => ['nullable'],
            'enabled' => ['nullable'],
        ];
    }

    /**
     * Get sanitized data from the request.
     */
    public function getSanitized(): array
    {
        $sanitized = $this->validated();
        
        // Ensure active and enabled are boolean values
        $sanitized['active'] = $this->boolean('active', true);
        $sanitized['enabled'] = $this->boolean('enabled', true);
        
        return $sanitized;
    }
}
