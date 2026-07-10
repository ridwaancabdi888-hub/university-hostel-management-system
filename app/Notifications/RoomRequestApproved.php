<?php

namespace App\Notifications;

use App\Models\RoomRequest;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Messages\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomRequestApproved extends Notification
{
    use Queueable;

    public function __construct(public RoomRequest $roomRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Room Request Approved')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your request for room {$this->roomRequest->room->room_number} has been approved.")
            ->action('View Dashboard', route('dashboard'));
    }

    public function toSms(object $notifiable): SmsMessage
    {
        return (new SmsMessage)->content("Your request for room {$this->roomRequest->room->room_number} has been approved.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Room Request Approved',
            'message' => "Your request for room {$this->roomRequest->room->room_number} has been approved.",
            'url' => route('dashboard'),
        ];
    }
}
