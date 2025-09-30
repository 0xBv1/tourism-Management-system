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
            'invoice_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the sanitized data from the request.
     */
    public function getSanitized(): array
    {
        $data = $this->validated();
        
        // Generate invoice ID if not provided
        if (empty($data['invoice_id'])) {
            $data['invoice_id'] = $this->generateInvoiceId();
        }
        
        // Generate reference number if not provided
        if (empty($data['reference_number'])) {
            $data['reference_number'] = $this->generateReferenceNumber();
        }
        
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

    /**
     * Generate a unique invoice ID
     */
    private function generateInvoiceId(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $random;
    }

    /**
     * Generate a unique reference number
     */
    private function generateReferenceNumber(): string
    {
        $prefix = 'PAY';
        $date = now()->format('Ymd');
        
        // Get the last payment reference number for today
        $lastPayment = \App\Models\Payment::whereDate('created_at', now()->toDateString())
            ->whereNotNull('reference_number')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPayment && $lastPayment->reference_number) {
            // Extract the sequence number from the last reference
            $lastRef = $lastPayment->reference_number;
            if (preg_match('/PAY-' . $date . '-(\d+)/', $lastRef, $matches)) {
                $sequence = (int) $matches[1] + 1;
            } else {
                $sequence = 1;
            }
        } else {
            $sequence = 1;
        }
        
        // Format sequence with leading zeros (4 digits)
        $sequenceFormatted = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequenceFormatted;
    }
}

