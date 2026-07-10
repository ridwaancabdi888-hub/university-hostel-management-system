<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlockRequest extends FormRequest
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
            'hostel_id' => ['required', 'integer', 'exists:hostels,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('blocks')
                    ->where(fn ($query) => $query->where('hostel_id', $this->input('hostel_id')))
                    ->ignore($this->route('block')),
            ],
            'code' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
