<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Guide Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'nationality' => 'Nationality',
            'languages' => 'Languages',
            'specializations' => 'Specializations',
            'experience_years' => 'Experience Years',
            'city_id' => 'City',
            'price_per_hour' => 'Price Per Hour',
            'price_per_day' => 'Price Per Day',
            'currency' => 'Currency',
            'bio' => 'Bio',
            'certifications' => 'Certifications',
            'profile_image' => 'Profile Image',
            'status' => 'Status',
            'active' => 'Active',
            'enabled' => 'Enabled',
            'availability_schedule' => 'Availability Schedule',
            'emergency_contact' => 'Emergency Contact',
            'emergency_phone' => 'Emergency Phone',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'languages' => ['required', 'array', 'min:1'],
            'languages.*' => ['string', 'max:50'],
            'specializations' => ['nullable', 'array'],
            'specializations.*' => ['string', 'max:100'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'city_id' => ['required', 'exists:cities,id'],
            'price_per_hour' => ['nullable', 'numeric', 'min:0'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'bio' => ['nullable', 'string'],
            'certifications' => ['nullable', 'array'],
            'certifications.*' => ['string', 'max:200'],
            'profile_image' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::enum(ResourceStatus::class)],
            'active' => ['nullable', 'boolean'],
            'enabled' => ['nullable', 'boolean'],
            'availability_schedule' => ['nullable', 'array'],
            'availability_schedule.monday' => ['nullable', 'boolean'],
            'availability_schedule.tuesday' => ['nullable', 'boolean'],
            'availability_schedule.wednesday' => ['nullable', 'boolean'],
            'availability_schedule.thursday' => ['nullable', 'boolean'],
            'availability_schedule.friday' => ['nullable', 'boolean'],
            'availability_schedule.saturday' => ['nullable', 'boolean'],
            'availability_schedule.sunday' => ['nullable', 'boolean'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'emergency_phone' => ['nullable', 'string', 'max:20'],
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




