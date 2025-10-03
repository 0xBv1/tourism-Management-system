<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DahabiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Dahabia Name',
            'description' => 'Description',
            'city_id' => 'City',
            'capacity' => 'Capacity',
            'price_per_person' => 'Price Per Person',
            'price_per_charter' => 'Price Per Charter',
            'currency' => 'Currency',
            'departure_location' => 'Departure Location',
            'arrival_location' => 'Arrival Location',
            'route_description' => 'Route Description',
            'sailing_schedule' => 'Sailing Schedule',
            'meal_plan' => 'Meal Plan',
           
          
            'active' => 'Active',
            'enabled' => 'Enabled',
            'crew_count' => 'Crew Count',
            'duration_nights' => 'Duration (Nights)',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city_id' => ['required', 'exists:cities,id'],         
            'capacity' => ['required', 'integer', 'min:1'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'price_per_charter' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'departure_location' => ['required', 'string', 'max:255'],
            'arrival_location' => ['required', 'string', 'max:255'],
            'route_description' => ['nullable', 'string'],
            'sailing_schedule' => ['nullable', 'array'],
            'sailing_schedule.*' => ['string', 'max:500'],
            'meal_plan' => ['nullable', 'string', 'max:100'],
            'active' => ['nullable'],
            'enabled' => ['nullable'],
            'crew_count' => ['nullable', 'integer', 'min:1'],
            'duration_nights' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['active'] = $this->filled('active');
        $data['enabled'] = $this->filled('enabled');
        return $data;
    }
}
