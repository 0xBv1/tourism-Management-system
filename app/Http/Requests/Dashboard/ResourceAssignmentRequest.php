<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'resource_type' => 'Resource Type',
            'resource_id' => 'Resource',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'quantity' => 'Quantity',
            'unit_price' => 'Unit Price',
            'currency' => 'Currency',
            'special_requirements' => 'Special Requirements',
            'notes' => 'Notes',
        ];
    }

    public function rules(): array
    {
        return [
            'resource_type' => ['required', 'in:hotel,vehicle,guide,representative'],
            'resource_id' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:3'],
            'special_requirements' => ['nullable', 'array'],
            'special_requirements.*' => ['string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['quantity'] = $data['quantity'] ?? 1;
        return $data;
    }
}




