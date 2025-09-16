<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_verified' => $this->boolean('is_verified'),
            'is_active' => $this->boolean('is_active'),
            'commission_rate' => $this->filled('commission_rate') ? (float) $this->input('commission_rate') : null,
        ]);
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            // User account fields
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email,' . ($this->supplier?->user_id ?? ''),
            'user_phone' => 'required|string|max:20',
            
            // Supplier profile fields
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255|unique:suppliers,company_email,' . ($this->supplier?->id ?? ''),
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'payment_info' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:100',
            'business_license' => 'nullable|string|max:100',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'is_verified' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ];

        // Password is required only for new suppliers
        if (!$this->supplier) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_name.required' => 'User name is required.',
            'user_name.max' => 'User name cannot exceed 255 characters.',
            'user_email.required' => 'User email is required.',
            'user_email.email' => 'Please enter a valid email address.',
            'user_email.unique' => 'This email is already registered.',
            'user_phone.required' => 'User phone number is required.',
            'user_phone.max' => 'User phone number cannot exceed 20 characters.',
            'phone_code.max' => 'Phone code cannot exceed 10 characters.',
            'company_name.required' => 'Company name is required.',
            'company_name.max' => 'Company name cannot exceed 255 characters.',
            'company_email.required' => 'Company email is required.',
            'company_email.email' => 'Please enter a valid company email address.',
            'company_email.unique' => 'This company email is already registered.',
            'phone.required' => 'Company phone number is required.',
            'phone.max' => 'Company phone number cannot exceed 20 characters.',
            'address.required' => 'Address is required.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'payment_info.max' => 'Payment information cannot exceed 1000 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
            'website.url' => 'Please enter a valid website URL.',
            'website.max' => 'Website URL cannot exceed 255 characters.',
            'tax_number.max' => 'Tax number cannot exceed 100 characters.',
            'business_license.max' => 'Business license cannot exceed 100 characters.',
            'commission_rate.numeric' => 'Commission rate must be a number.',
            'commission_rate.min' => 'Commission rate cannot be less than 0%.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_name' => 'user name',
            'user_email' => 'user email',
            'user_phone' => 'user phone number',
            'phone_code' => 'phone code',
            'company_name' => 'company name',
            'company_email' => 'company email',
            'phone' => 'company phone number',
            'address' => 'address',
            'payment_info' => 'payment information',
            'description' => 'description',
            'website' => 'website',
            'tax_number' => 'tax number',
            'business_license' => 'business license',
            'commission_rate' => 'commission rate',
            'is_verified' => 'verification status',
            'is_active' => 'active status',
            'password' => 'password',
        ];
    }
}
