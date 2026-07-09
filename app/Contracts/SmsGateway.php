<?php

namespace App\Contracts;

/**
 * Abstraction over whichever SMS provider is actually wired up.
 *
 * Swapping providers (Twilio, Vonage, Africa's Talking, etc.) means
 * writing one class that implements this interface and rebinding it in
 * AppServiceProvider — nothing in the notification layer needs to change.
 */
interface SmsGateway
{
    public function send(string $to, string $message): void;
}
