<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Enums\YearLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StudentRequest extends FormRequest
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
        $student = $this->route('student');

        return [
            // Account
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student?->user_id)],
            'password' => [$student ? 'nullable' : 'required', Password::defaults()],

            // University details
            'student_id' => ['required', 'string', 'max:50', Rule::unique('student_profiles', 'student_id')->ignore($student)],
            'course' => ['required', 'string', 'max:255'],
            'year_level' => ['required', Rule::enum(YearLevel::class)],
            'status' => ['required', Rule::enum(StudentStatus::class)],

            // Profile
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],

            // Guardian information
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_relationship' => ['nullable', 'string', 'max:100'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_email' => ['nullable', 'string', 'email', 'max:255'],
            'guardian_address' => ['nullable', 'string', 'max:255'],

            // Emergency contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
