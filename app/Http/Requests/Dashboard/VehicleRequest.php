<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Vehicle Name',
            'type' => 'Vehicle Type',
            'brand' => 'Brand',
            'model' => 'Model',
            'year' => 'Year',
            'license_plate' => 'License Plate',
            'capacity' => 'Capacity',
            'description' => 'Description',
            'city_id' => 'City',
            'driver_name' => 'Driver Name',
            'driver_phone' => 'Driver Phone',
            'driver_license' => 'Driver License',
            'price_per_hour' => 'Price Per Hour',
            'price_per_day' => 'Price Per Day',
            'currency' => 'Currency',
            'fuel_type' => 'Fuel Type',
            'transmission' => 'Transmission',
            'features' => 'Features',
            'images' => 'Images',
            'status' => 'Status',
            'active' => 'Active',
            'enabled' => 'Enabled',
            'insurance_expiry' => 'Insurance Expiry',
            'registration_expiry' => 'Registration Expiry',
            'last_maintenance' => 'Last Maintenance',
            'next_maintenance' => 'Next Maintenance',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:100'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'license_plate' => ['required', 'string', 'max:20'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'city_id' => ['required', 'exists:cities,id'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:20'],
            'driver_license' => ['nullable', 'string', 'max:50'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'fuel_type' => ['nullable', 'string', 'max:50'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'features' => ['nullable', 'array'],
            'features.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['string', 'max:500'],
            'status' => ['required', Rule::enum(ResourceStatus::class)],
            'active' => ['nullable', 'boolean'],
            'enabled' => ['nullable', 'boolean'],
            'insurance_expiry' => ['nullable', 'date'],
            'registration_expiry' => ['nullable', 'date'],
            'last_maintenance' => ['nullable', 'date'],
            'next_maintenance' => ['nullable', 'date'],
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




