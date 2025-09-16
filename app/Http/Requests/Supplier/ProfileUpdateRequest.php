<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255|unique:suppliers,company_email,' . (auth()->user()->supplier?->id ?? ''),
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'payment_info' => 'nullable|string|max:1000',
            'description' => 'nullable|string|max:2000',
            'website' => 'nullable|url|max:255',
            'tax_number' => 'nullable|string|max:100',
            'business_license' => 'nullable|string|max:100',
            'logo' => 'nullable|string|max:255',
        ];

        // Only validate commission_rate if user is admin or supplier admin
        if (auth()->user()->hasRole(['Admin', 'Supplier Admin'])) {
            $rules['commission_rate'] = 'nullable|numeric|min:0|max:100';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.max' => 'Company name cannot exceed 255 characters.',
            'company_email.required' => 'Company email is required.',
            'company_email.email' => 'Please enter a valid email address.',
            'company_email.unique' => 'This company email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'address.required' => 'Address is required.',
            'address.max' => 'Address cannot exceed 1000 characters.',
            'payment_info.max' => 'Payment information cannot exceed 1000 characters.',
            'description.max' => 'Description cannot exceed 2000 characters.',
            'website.url' => 'Please enter a valid website URL.',
            'tax_number.max' => 'Tax number cannot exceed 100 characters.',
            'business_license.max' => 'Business license cannot exceed 100 characters.',
            'logo.max' => 'Logo path cannot exceed 255 characters.',
            'commission_rate.numeric' => 'Commission rate must be a number.',
            'commission_rate.min' => 'Commission rate cannot be less than 0%.',
            'commission_rate.max' => 'Commission rate cannot exceed 100%.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'company name',
            'company_email' => 'company email',
            'phone' => 'phone number',
            'address' => 'address',
            'payment_info' => 'payment information',
            'description' => 'description',
            'website' => 'website',
            'tax_number' => 'tax number',
            'business_license' => 'business license',
            'logo' => 'logo',
            'commission_rate' => 'commission rate',
        ];
    }
}
