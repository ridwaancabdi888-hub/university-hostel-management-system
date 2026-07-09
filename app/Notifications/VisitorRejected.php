<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitorRejected extends Notification
{
    use Queueable;

    public function __construct(public Visitor $visitor) {}

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
            ->subject('Visitor Request Declined')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your visitor {$this->visitor->name} was not approved for {$this->visitor->expected_at->format('M j, Y g:i A')}.");

        if ($this->visitor->rejection_reason) {
            $message->line("Reason: {$this->visitor->rejection_reason}");
        }

        return $message->action('View Details', route('visitors.show', $this->visitor));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Visitor Declined',
            'message' => "Your visitor {$this->visitor->name} was not approved.",
            'url' => route('visitors.show', $this->visitor),
        ];
    }
}
