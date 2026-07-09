<?php

namespace App\Notifications\Channels;

use App\Contracts\SmsGateway;
use App\Notifications\Messages\SmsMessage;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(private readonly SmsGateway $gateway) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $phone = $notifiable->routeNotificationFor('sms', $notification);

        if (! $phone) {
            return;
        }

        $message = $notification->toSms($notifiable);
        $content = $message instanceof SmsMessage ? $message->content : (string) $message;

        if ($content === '') {
            return;
        }

        $this->gateway->send($phone, $content);
    }
}
