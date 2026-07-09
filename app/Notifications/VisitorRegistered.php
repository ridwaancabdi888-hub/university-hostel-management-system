<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitorRegistered extends Notification
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
        $student = $this->visitor->studentProfile->user;

        return (new MailMessage)
            ->subject('New Visitor Awaiting Approval')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->visitor->name} has registered as a visitor for {$student->name} ({$this->visitor->expected_at->format('M j, Y g:i A')}).")
            ->line("Purpose: {$this->visitor->purpose}")
            ->action('Review Visitor', route('visitors.show', $this->visitor));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $student = $this->visitor->studentProfile->user;

        return [
            'title' => 'New Visitor Registration',
            'message' => "{$this->visitor->name} is awaiting approval to visit {$student->name}.",
            'url' => route('visitors.show', $this->visitor),
        ];
    }
}
