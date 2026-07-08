<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FloorRequest extends FormRequest
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
            'block_id' => ['required', 'integer', 'exists:blocks,id'],
            'name' => ['required', 'string', 'max:255'],
            'level' => [
                'required', 'integer', 'min:0', 'max:200',
                Rule::unique('floors')
                    ->where(fn ($query) => $query->where('block_id', $this->input('block_id')))
                    ->ignore($this->route('floor')),
            ],
        ];
    }
}
