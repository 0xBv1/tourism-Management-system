<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\InquiryStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InquiryRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function attributes(): array
    {
        return [
            "guest_name" => "Guest Name",
            "email" => "Email",
            "phone" => "Phone",
            "arrival_date" => "Arrival Date",
            "departure_date" => "Departure Date",
            "number_pax" => "Number of Pax",
            "tour_name" => "Tour Name",
            "nationality" => "Nationality",
            "subject" => "Subject",
            "status" => "Status",
            "assigned_to" => "Assigned To",
            "total_amount" => "Total Amount",
            "paid_amount" => "Paid Amount",
            "remaining_amount" => "Remaining Amount",
            "payment_method" => "Payment Method",
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
            'guest_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'arrival_date' => ['nullable', 'date', 'after_or_equal:today'],
            'departure_date' => ['nullable', 'date', 'after_or_equal:arrival_date'],
            'number_pax' => ['nullable', 'integer', 'min:1'],
            'tour_name' => ['nullable', 'string', 'max:255'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::enum(InquiryStatus::class)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0'],
            'remaining_amount' => ['nullable', 'numeric'],
            'payment_method' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get the validated fields.
     *
     * @return array
     */
    public function getSanitized(): array
    {
        return $this->validated();
    }
}
