<?php

namespace App\Notifications;

use App\Models\RoomRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomRequestSubmitted extends Notification
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
        $student = $this->roomRequest->studentProfile->user;

        return (new MailMessage)
            ->subject('New Room Request Awaiting Approval')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$student->name} has requested room {$this->roomRequest->room->room_number}.")
            ->action('Review Request', route('room-requests.index'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $student = $this->roomRequest->studentProfile->user;

        return [
            'title' => 'New Room Request',
            'message' => "{$student->name} has requested room {$this->roomRequest->room->room_number}.",
            'url' => route('room-requests.index'),
        ];
    }
}
