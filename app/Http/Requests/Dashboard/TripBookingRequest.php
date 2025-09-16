<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\TripBooking;

class TripBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'trip_id' => 'required|exists:trips,id',
            'client_id' => 'nullable|exists:clients,id',
            'passenger_name' => 'required|string|max:255',
            'passenger_email' => 'required|email|max:255',
            'passenger_phone' => 'required|string|max:20',
            'adults_count' => 'required|integer|min:1|max:50',
            'children_count' => 'nullable|integer|min:0|max:50',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:' . implode(',', TripBooking::getStatuses()),
            'notes' => 'nullable|string|max:1000',
        ];

        // Add conditional rules based on operation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // For updating existing bookings
            $rules['booking_reference'] = 'required|string|max:50|unique:trip_bookings,booking_reference,' . $this->route('trip_booking');
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'trip_id.required' => 'Please select a trip.',
            'trip_id.exists' => 'The selected trip does not exist.',
            'client_id.exists' => 'The selected client does not exist.',
            'passenger_name.required' => 'Passenger name is required.',
            'passenger_name.string' => 'Passenger name must be a string.',
            'passenger_name.max' => 'Passenger name cannot exceed 255 characters.',
            'passenger_email.required' => 'Passenger email is required.',
            'passenger_email.email' => 'Please enter a valid email address.',
            'passenger_email.max' => 'Passenger email cannot exceed 255 characters.',
            'passenger_phone.required' => 'Passenger phone is required.',
            'passenger_phone.string' => 'Passenger phone must be a string.',
            'passenger_phone.max' => 'Passenger phone cannot exceed 20 characters.',
            'adults_count.required' => 'Number of adults is required.',
            'adults_count.integer' => 'Number of adults must be a whole number.',
            'adults_count.min' => 'Number of adults must be at least 1.',
            'adults_count.max' => 'Number of adults cannot exceed 50.',
            'children_count.integer' => 'Number of children must be a whole number.',
            'children_count.min' => 'Number of children cannot be negative.',
            'children_count.max' => 'Number of children cannot exceed 50.',
            'total_price.required' => 'Total price is required.',
            'total_price.numeric' => 'Total price must be a number.',
            'total_price.min' => 'Total price must be at least 0.',
            'status.required' => 'Please select a status.',
            'status.in' => 'The selected status is invalid.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
            'booking_reference.required' => 'Booking reference is required.',
            'booking_reference.string' => 'Booking reference must be a string.',
            'booking_reference.max' => 'Booking reference cannot exceed 50 characters.',
            'booking_reference.unique' => 'This booking reference is already taken.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'trip_id' => 'trip',
            'client_id' => 'client',
            'passenger_name' => 'passenger name',
            'passenger_email' => 'passenger email',
            'passenger_phone' => 'passenger phone',
            'adults_count' => 'number of adults',
            'children_count' => 'number of children',
            'total_price' => 'total price',
            'status' => 'status',
            'notes' => 'notes',
            'booking_reference' => 'booking reference',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'children_count' => $this->children_count ?: 0,
            'notes' => $this->notes ?: null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if trip has enough available seats
            $trip = \App\Models\Trip::find($this->trip_id);
            if ($trip) {
                $totalPassengers = $this->adults_count + ($this->children_count ?? 0);
                
                // For new bookings, check available seats
                if ($this->isMethod('POST')) {
                    if (!$trip->hasAvailableSeats($totalPassengers)) {
                        $validator->errors()->add('trip_id', 'Trip does not have enough available seats for the requested number of passengers.');
                    }
                }
                
                // For updates, check if the change is valid
                if (($this->isMethod('PUT') || $this->isMethod('PATCH')) && $this->route('trip_booking')) {
                    $booking = TripBooking::find($this->route('trip_booking'));
                    if ($booking) {
                        $currentPassengers = $booking->adults_count + $booking->children_count;
                        $newPassengers = $totalPassengers;
                        $difference = $newPassengers - $currentPassengers;
                        
                        if ($difference > 0 && $trip->available_seats < $difference) {
                            $validator->errors()->add('trip_id', 'Trip does not have enough available seats for the increased number of passengers.');
                        }
                    }
                }
            }
        });
    }
}
