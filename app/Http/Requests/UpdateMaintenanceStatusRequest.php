<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceStatus;
use App\Enums\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMaintenanceStatusRequest extends FormRequest
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
        return [
            'status' => ['required', Rule::enum(MaintenanceStatus::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * A student may only confirm a fix or reopen it while the ticket is
     * awaiting their verification — every other transition is staff-only.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty() || ! $this->user()->hasRole(Role::Student)) {
                return;
            }

            $ticket = $this->route('ticket');
            $target = $this->input('status');

            $allowed = $ticket->status === MaintenanceStatus::Verification
                && in_array($target, [MaintenanceStatus::Completed->value, MaintenanceStatus::InProgress->value], true);

            if (! $allowed) {
                $validator->errors()->add('status', 'You can only confirm or reopen a ticket that is awaiting your verification.');
            }
        });
    }
}
