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
        $room = $this->route('room');

        // Capacity can never drop below the beds currently occupied by
        // active allocations — occupancy itself is derived automatically
        // from those allocations and is not directly editable here.
        $minCapacity = max(1, $room?->occupied_beds ?? 1);

        return [
            'floor_id' => ['required', 'integer', 'exists:floors,id'],
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'room_number' => [
                'required', 'string', 'max:20',
                Rule::unique('rooms')
                    ->where(fn ($query) => $query->where('floor_id', $this->input('floor_id')))
                    ->ignore($this->route('room')),
            ],
            'capacity' => ['required', 'integer', "min:{$minCapacity}", 'max:20'],
            'status' => ['required', Rule::enum(RoomStatus::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'photo_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
