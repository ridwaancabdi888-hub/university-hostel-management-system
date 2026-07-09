<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VisitorRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:100'],
            'purpose' => ['required', 'string', 'max:1000'],
            'expected_at' => ['required', 'date', 'after:now'],
        ];

        // Staff registering on a student's behalf must say who it's for;
        // a student registering their own visitor doesn't choose this.
        if (! $this->route('visitor') && ! $this->user()->hasRole(Role::Student)) {
            $rules['student_profile_id'] = ['required', 'integer', 'exists:student_profiles,id'];
        }

        return $rules;
    }
}
