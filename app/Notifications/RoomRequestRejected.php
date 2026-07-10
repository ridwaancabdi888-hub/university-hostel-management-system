<?php

namespace App\Notifications;

use App\Models\RoomRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomRequestRejected extends Notification
{
    use Queueable;

    public function __construct(public RoomRequest $roomRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Room Request Declined')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your request for room {$this->roomRequest->room->room_number} was not approved.");

        if ($this->roomRequest->rejection_reason) {
            $message->line("Reason: {$this->roomRequest->rejection_reason}");
        }

        return $message->action('View Available Rooms', route('room-requests.index'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Room Request Declined',
            'message' => "Your request for room {$this->roomRequest->room->room_number} was not approved.",
            'url' => route('room-requests.index'),
        ];
    }
}
