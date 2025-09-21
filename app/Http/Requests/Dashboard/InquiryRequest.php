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
            "name" => "Name",
            "email" => "Email",
            "phone" => "Phone",
            "subject" => "Subject",
            "message" => "Message",
            "status" => "Status",
            "admin_notes" => "Admin Notes",
            "assigned_to" => "Assigned To",
            "user1_id" => "Confirmation User 1",
            "user2_id" => "Confirmation User 2",
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'status' => ['required', Rule::enum(InquiryStatus::class)],
            'admin_notes' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'user1_id' => ['nullable', 'exists:users,id'],
            'user2_id' => ['nullable', 'exists:users,id', 'different:user1_id'],
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
