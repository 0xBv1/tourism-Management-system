<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ResourceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'name' => 'Ticket Name',
            'description' => 'Description',
            'city_id' => 'City',
            'price_per_person' => 'Price Per Person',
            'currency' => 'Currency',
            'duration_hours' => 'Duration (Hours)',
            'images' => 'Images',
          
            'active' => 'Active',
            'enabled' => 'Enabled',
            'min_age' => 'Minimum Age',
            'max_age' => 'Maximum Age',
            'max_participants' => 'Maximum Participants',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'city_id' => ['required', 'exists:cities,id'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'duration_hours' => ['nullable', 'numeric', 'min:0'],
                     
            'active' => ['nullable'],
            'enabled' => ['nullable'],
            'min_age' => ['nullable', 'integer', 'min:0', 'max:18'],
            'max_age' => ['nullable', 'integer', 'min:1', 'gt:min_age'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
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
