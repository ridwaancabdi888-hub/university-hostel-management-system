<?php

namespace App\Http\Requests;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class TransferRoomRequest extends FormRequest
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
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'bed_number' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance with the cross-field checks that
     * prevent transferring a student into an overbooked room or bed.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $room = Room::find($this->input('room_id'));
            $bedNumber = (int) $this->input('bed_number');

            if ($room && $room->status !== RoomStatus::Available) {
                $validator->errors()->add('room_id', 'This room is not available for allocation.');
            }

            if ($room && $bedNumber > $room->capacity) {
                $validator->errors()->add('bed_number', "This room only has {$room->capacity} beds.");
            }

            if ($room && $bedNumber <= $room->capacity && ! in_array($bedNumber, $room->availableBedNumbers(), true)) {
                $validator->errors()->add('bed_number', 'This bed is already occupied.');
            }
        });
    }
}
