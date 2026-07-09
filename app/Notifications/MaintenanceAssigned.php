<?php

namespace App\Notifications;

use App\Models\MaintenanceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceAssigned extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceRequest $ticket) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ticket Assigned to You')
            ->greeting("Hi {$notifiable->name},")
            ->line("You've been assigned to: {$this->ticket->title}")
            ->line("Priority: {$this->ticket->priority->label()}")
            ->action('View Ticket', route('maintenance.show', $this->ticket));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Ticket Assigned',
            'message' => "You've been assigned to \"{$this->ticket->title}\".",
            'url' => route('maintenance.show', $this->ticket),
        ];
    }
}
