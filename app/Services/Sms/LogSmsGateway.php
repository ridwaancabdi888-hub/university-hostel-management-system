<?php

namespace App\Services\Sms;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Default SmsGateway binding: writes to the "sms" log channel instead of
 * calling a real provider. Bind a different implementation of SmsGateway
 * in AppServiceProvider once a real account/API key exists.
 */
class LogSmsGateway implements SmsGateway
{
    public function send(string $to, string $message): void
    {
        Log::channel('sms')->info("SMS to {$to}: {$message}");
    }
}
