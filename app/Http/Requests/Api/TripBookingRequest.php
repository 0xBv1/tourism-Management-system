<?php

namespace App\Http\Requests\Api;

use App\Traits\Response\RequestValidationErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class TripBookingRequest extends FormRequest
{
    use RequestValidationErrorResponse;

    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'integer', 'exists:trips,id'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'number_of_passengers' => ['required', 'integer', 'min:1', 'max:50'],
            'selected_seats' => ['nullable', 'array'],
            'selected_seats.*' => ['string', 'max:10'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'trip_id.required' => 'Trip ID is required.',
            'trip_id.exists' => 'Selected trip does not exist.',
            'contact_name.required' => 'Contact name is required.',
            'contact_name.max' => 'Contact name cannot exceed 255 characters.',
            'contact_email.required' => 'Contact email is required.',
            'contact_email.email' => 'Please provide a valid email address.',
            'contact_email.max' => 'Contact email cannot exceed 255 characters.',
            'contact_phone.required' => 'Contact phone is required.',
            'contact_phone.max' => 'Contact phone cannot exceed 20 characters.',
            'number_of_passengers.required' => 'Number of passengers is required.',
            'number_of_passengers.min' => 'Number of passengers must be at least 1.',
            'number_of_passengers.max' => 'Number of passengers cannot exceed 50.',
            'selected_seats.array' => 'Selected seats must be an array.',
            'selected_seats.*.string' => 'Each seat must be a string.',
            'selected_seats.*.max' => 'Each seat identifier cannot exceed 10 characters.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.after_or_equal' => 'Booking date must be today or a future date.',
            'special_requests.max' => 'Special requests cannot exceed 1000 characters.',
        ];
    }

    public function getSanitized()
    {
        return $this->validated();
    }
}
