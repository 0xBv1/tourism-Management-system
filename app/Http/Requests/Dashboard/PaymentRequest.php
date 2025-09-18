<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'exists:booking_files,id'],
            'gateway' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(PaymentStatus::class)],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'transaction_request' => ['nullable', 'array'],
            'transaction_verification' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the sanitized data from the request.
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        
        // Set paid_at if status is paid and paid_at is not provided
        if ($data['status'] === PaymentStatus::PAID->value && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }
        
        // Clear paid_at if status is not paid
        if ($data['status'] !== PaymentStatus::PAID->value) {
            $data['paid_at'] = null;
        }
        
        return $data;
    }
}

