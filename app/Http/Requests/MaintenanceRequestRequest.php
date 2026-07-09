<?php

namespace App\Http\Requests;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaintenanceRequestRequest extends FormRequest
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
        $rules = [
            'category' => ['required', Rule::enum(MaintenanceCategory::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', Rule::enum(MaintenancePriority::class)],
        ];

        // Staff submitting on a student's behalf must say who it's for;
        // a student submitting their own ticket doesn't choose this. Only
        // relevant at creation — who a ticket belongs to can't be changed.
        if (! $this->route('ticket') && ! $this->user()->hasRole(Role::Student)) {
            $rules['student_profile_id'] = ['required', 'integer', 'exists:student_profiles,id'];
        }

        return $rules;
    }
}
