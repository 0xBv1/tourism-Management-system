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
            'price_range' => 'Price Range',
            'currency' => 'Currency',
            'cuisines' => 'Cuisines',
            'images' => 'Images',
            'status' => 'Status',
            'active' => 'Active',
            'enabled' => 'Enabled',
            'capacity' => 'Capacity',
            'reservation_required' => 'Reservation Required',
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
            'price_range' => ['nullable', 'string', 'max:50'],
            'currency' => ['required', 'string', 'max:3'],
                     
            'active' => ['nullable'],
            'enabled' => ['nullable'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'reservation_required' => ['nullable'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['active'] = $this->filled('active');
        $data['enabled'] = $this->filled('enabled');
        $data['reservation_required'] = $this->filled('reservation_required');
        return $data;
    }
}
