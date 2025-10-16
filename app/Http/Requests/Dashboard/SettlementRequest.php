<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\SettlementType;
use App\Enums\CommissionType;

class SettlementRequest extends FormRequest
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
            'resource_type' => 'required|in:guide,representative',
            'resource_id' => 'required|integer',
            'settlement_type' => 'required|in:' . implode(',', SettlementType::all()),
            'year' => 'required|integer|min:2020|max:2030',
            'commission_type' => 'required|in:' . implode(',', CommissionType::all()),
            'commission_value' => 'required_if:commission_type,percentage,fixed|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'deductions' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ];

        // Add month validation for monthly settlements
        if ($this->settlement_type === SettlementType::MONTHLY->value) {
            $rules['month'] = 'required|integer|between:1,12';
        }

        // Add date range validation for custom settlements
        if ($this->settlement_type === SettlementType::CUSTOM->value) {
            $rules['start_date'] = 'required|date';
            $rules['end_date'] = 'required|date|after:start_date';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'resource_type.required' => 'نوع المورد مطلوب',
            'resource_type.in' => 'نوع المورد يجب أن يكون مرشد أو مندوب',
            'resource_id.required' => 'المورد مطلوب',
            'resource_id.integer' => 'معرف المورد يجب أن يكون رقم',
            'settlement_type.required' => 'نوع التسوية مطلوب',
            'settlement_type.in' => 'نوع التسوية غير صحيح',
            'month.required_if' => 'الشهر مطلوب للتسوية الشهرية',
            'month.between' => 'الشهر يجب أن يكون بين 1 و 12',
            'year.required' => 'السنة مطلوبة',
            'year.min' => 'السنة يجب أن تكون 2020 أو أحدث',
            'year.max' => 'السنة يجب أن تكون 2030 أو أقل',
            'start_date.required_if' => 'تاريخ البداية مطلوب للتسوية المخصصة',
            'start_date.date' => 'تاريخ البداية غير صحيح',
            'end_date.required_if' => 'تاريخ النهاية مطلوب للتسوية المخصصة',
            'end_date.date' => 'تاريخ النهاية غير صحيح',
            'end_date.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
            'commission_type.required' => 'نوع العمولة مطلوب',
            'commission_type.in' => 'نوع العمولة غير صحيح',
            'commission_value.required_if' => 'قيمة العمولة مطلوبة',
            'commission_value.numeric' => 'قيمة العمولة يجب أن تكون رقم',
            'commission_value.min' => 'قيمة العمولة يجب أن تكون أكبر من أو تساوي صفر',
            'tax_rate.numeric' => 'معدل الضريبة يجب أن يكون رقم',
            'tax_rate.min' => 'معدل الضريبة يجب أن يكون أكبر من أو يساوي صفر',
            'tax_rate.max' => 'معدل الضريبة يجب أن يكون أقل من أو يساوي 100',
            'deductions.numeric' => 'الخصومات يجب أن تكون رقم',
            'deductions.min' => 'الخصومات يجب أن تكون أكبر من أو تساوي صفر',
            'bonuses.numeric' => 'المكافآت يجب أن تكون رقم',
            'bonuses.min' => 'المكافآت يجب أن تكون أكبر من أو تساوي صفر',
            'notes.max' => 'الملاحظات يجب أن تكون أقل من 1000 حرف',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'resource_type' => 'نوع المورد',
            'resource_id' => 'المورد',
            'settlement_type' => 'نوع التسوية',
            'month' => 'الشهر',
            'year' => 'السنة',
            'start_date' => 'تاريخ البداية',
            'end_date' => 'تاريخ النهاية',
            'commission_type' => 'نوع العمولة',
            'commission_value' => 'قيمة العمولة',
            'tax_rate' => 'معدل الضريبة',
            'deductions' => 'الخصومات',
            'bonuses' => 'المكافآت',
            'notes' => 'الملاحظات',
        ];
    }
}