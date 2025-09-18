<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Hotel Name',
            'description' => 'Description',
            'address' => 'Address',
            'city_id' => 'City',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'star_rating' => 'Star Rating',
            'total_rooms' => 'Total Rooms',
            'available_rooms' => 'Available Rooms',
            'price_per_night' => 'Price Per Night',
            'currency' => 'Currency',
            'amenities' => 'Amenities',
            'images' => 'Images',
            'status' => 'Status',
            'active' => 'Active',
            'enabled' => 'Enabled',
            'check_in_time' => 'Check-in Time',
            'check_out_time' => 'Check-out Time',
            'cancellation_policy' => 'Cancellation Policy',
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
            'star_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'total_rooms' => ['required', 'integer', 'min:1'],
            'available_rooms' => ['required', 'integer', 'min:0', 'lte:total_rooms'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:500'],
            'status' => ['required', Rule::enum(ResourceStatus::class)],
            'active' => ['nullable', 'boolean'],
            'enabled' => ['nullable', 'boolean'],
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'cancellation_policy' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'enabled' => ['nullable'],
            'active' => ['nullable'],
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




