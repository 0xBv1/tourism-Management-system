<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Restaurant Name',
            'description' => 'Description',
            'address' => 'Address',
            'city_id' => 'City',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'cuisine_type' => 'Cuisine Type',
            'price_range' => 'Price Range',
            'price_per_meal' => 'Price Per Meal',
            'currency' => 'Currency',
            'cuisines' => 'Cuisines',
            'features' => 'Features',
            'amenities' => 'Amenities',
            'images' => 'Images',
            'status' => 'Status',
            'active' => 'Active',
            'enabled' => 'Enabled',
            'opening_hours' => 'Opening Hours',
            'capacity' => 'Capacity',
            'reservation_required' => 'Reservation Required',
            'dress_code' => 'Dress Code',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'city_id' => ['required', 'exists:cities,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'cuisine_type' => ['required', 'string', 'max:100'],
            'price_range' => ['nullable', 'string', 'max:50'],
            'price_per_meal' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'cuisines' => ['nullable', 'array'],
            'cuisines.*' => ['string', 'max:100'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:100'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:500'],
            'status' => ['required', Rule::enum(ResourceStatus::class)],
            'active' => ['nullable', 'boolean'],
            'enabled' => ['nullable', 'boolean'],
            'opening_hours' => ['nullable', 'array'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'reservation_required' => ['nullable', 'boolean'],
            'dress_code' => ['nullable', 'string', 'max:100'],
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
