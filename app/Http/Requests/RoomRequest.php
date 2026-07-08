<?php

namespace App\Http\Requests;

use App\Enums\RoomStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoomRequest extends FormRequest
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
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_number' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms')
                    ->where(fn ($query) => $query->where('floor_id', $this->input('floor_id')))
                    ->ignore($this->route('room')),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'occupied_beds' => ['required', 'integer', 'min:0', 'lte:capacity'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
