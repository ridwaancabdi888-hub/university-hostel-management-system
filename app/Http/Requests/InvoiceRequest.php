<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class InvoiceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $invoice = $this->route('invoice');

        return [
            // Student and billing month are only set at creation time —
            // changing them afterwards would undermine the one-bill-per-
            // student-per-month guarantee "Generate Monthly Bills" relies on.
            'student_profile_id' => [
                $invoice ? 'prohibited' : 'required', 'integer', 'exists:student_profiles,id',
            ],
            'billing_month' => [
                $invoice ? 'prohibited' : 'required', 'date',
                Rule::unique('invoices')
                    ->where(fn ($query) => $query->where('student_profile_id', $this->input('student_profile_id'))
                        ->whereRaw('DATE_FORMAT(billing_month, "%Y-%m") = ?', [$this->date('billing_month')?->format('Y-m')]))
                    ->ignore($invoice),
            ],
            'rent_amount' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'utility_amount' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'late_fee_amount' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'discount_amount' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'due_date' => ['required', 'date'],
            'status' => [$invoice ? 'required' : 'prohibited', Rule::enum(InvoiceStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Ensure the discount never pushes the invoice total negative.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $chargeable = (float) $this->input('rent_amount') + (float) $this->input('utility_amount') + (float) $this->input('late_fee_amount');

            if ((float) $this->input('discount_amount') > $chargeable) {
                $validator->errors()->add('discount_amount', 'The discount cannot exceed the rent, utility, and late fee charges combined.');
            }
        });
    }
}
