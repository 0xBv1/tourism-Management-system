<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'trip_name' => 'Trip Name',
            'trip_type' => 'Trip Type',
            'departure_city' => 'Departure City',
            'arrival_city' => 'Arrival City',
            'travel_date' => 'Travel Date',
            'return_date' => 'Return Date',
            'departure_time' => 'Departure Time',
            'arrival_time' => 'Arrival Time',
            'seat_price' => 'Seat Price',
            'total_seats' => 'Total Seats',
            'available_seats' => 'Available Seats',
            'additional_notes' => 'Additional Notes',
            'amenities' => 'Amenities',
            'featured_image' => 'Featured Image',
            'images' => 'Gallery Images',
            'images.*' => 'Gallery Image',
            'enabled' => 'Enabled',
            'approved' => 'Approved',
            'rejection_reason' => 'Rejection Reason',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'trip_name' => ['nullable', 'string', 'max:255'],
            'trip_type' => ['required', 'string', Rule::in(['one_way', 'round_trip', 'special_discount'])],
            'departure_city' => ['required', 'string', 'max:255'],
            'arrival_city' => ['required', 'string', 'max:255'],
            'departure_city_id' => ['nullable', 'string', 'max:255'],
            'arrival_city_id' => ['nullable', 'string', 'max:255'],
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after:travel_date'],
            'departure_time' => ['required', 'date_format:H:i'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'seat_price' => ['required', 'numeric', 'min:0'],
            'total_seats' => ['required', 'integer', 'min:1'],
            'available_seats' => ['nullable', 'integer', 'min:0'],
            'additional_notes' => ['nullable', 'string'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['nullable', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
  'enabled' => ['nullable'],            'approved' => ['nullable', 'boolean'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        $data['enabled'] = $this->boolean('enabled');
        $data['approved'] = $this->boolean('approved');
        // Normalize cities from *_id if present (supplier uses names)
        if (empty($data['departure_city']) && !empty($data['departure_city_id'])) {
            $data['departure_city'] = $data['departure_city_id'];
        }
        if (empty($data['arrival_city']) && !empty($data['arrival_city_id'])) {
            $data['arrival_city'] = $data['arrival_city_id'];
        }
        // Auto-generate trip_name if missing
        if (empty($data['trip_name']) && !empty($data['departure_city']) && !empty($data['arrival_city'])) {
            $suffix = '';
            if (($data['trip_type'] ?? null) === 'round_trip') {
                $suffix = ' (Round Trip)';
            } elseif (($data['trip_type'] ?? null) === 'special_discount') {
                $suffix = ' (Special Discount)';
            }
            $data['trip_name'] = $data['departure_city'] . ' to ' . $data['arrival_city'] . $suffix;
        }
        unset($data['departure_city_id'], $data['arrival_city_id']);
        unset($data['featured_image'], $data['images']);
        return $data;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'departure_city' => $this->input('departure_city') ?: $this->input('departure_city_id'),
            'arrival_city' => $this->input('arrival_city') ?: $this->input('arrival_city_id'),
        ]);
    }
}
