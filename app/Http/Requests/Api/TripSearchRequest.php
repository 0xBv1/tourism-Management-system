<?php

namespace App\Http\Requests\Api;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class TripSearchRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'trip_type' => ['required', 'string', 'in:one_way,round_trip'],
            'departure_city_id' => ['required', 'integer', 'exists:cities,id'],
            'arrival_city_id' => ['required', 'integer', 'exists:cities,id'],
            'passengers' => ['required', 'integer', 'min:1', 'max:50'],
            'date_start' => ['required', 'date', 'after_or_equal:today'],
            'date_end' => ['nullable', 'date', 'after_or_equal:date_start'],
        ];
    }

    public function messages(): array
    {
        return [
            'trip_type.required' => 'Trip type is required.',
            'trip_type.in' => 'Trip type must be either one_way or round_trip.',
            'departure_city_id.required' => 'Departure city is required.',
            'departure_city_id.exists' => 'Selected departure city does not exist.',
            'arrival_city_id.required' => 'Arrival city is required.',
            'arrival_city_id.exists' => 'Selected arrival city does not exist.',
            'passengers.required' => 'Number of passengers is required.',
            'passengers.min' => 'Number of passengers must be at least 1.',
            'passengers.max' => 'Number of passengers cannot exceed 50.',
            'date_start.required' => 'Start date is required.',
            'date_start.after_or_equal' => 'Start date must be today or a future date.',
            'date_end.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }

    public function getSanitized()
    {
        return $this->validated();
    }
}
