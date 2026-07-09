<?php

namespace App\Notifications;

use App\Models\Visitor;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Messages\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitorApproved extends Notification
{
    use Queueable;

    public function __construct(public Visitor $visitor) {}

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
            ->subject('Visitor Approved')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your visitor {$this->visitor->name} has been approved for {$this->visitor->expected_at->format('M j, Y g:i A')}.")
            ->action('View Details', route('visitors.show', $this->visitor));
    }

    public function toSms(object $notifiable): SmsMessage
    {
        return (new SmsMessage)->content(
            "Your visitor {$this->visitor->name} is approved for {$this->visitor->expected_at->format('M j, g:i A')}."
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Visitor Approved',
            'message' => "Your visitor {$this->visitor->name} has been approved.",
            'url' => route('visitors.show', $this->visitor),
        ];
    }
}
