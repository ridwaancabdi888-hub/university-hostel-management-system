<?php

namespace App\Providers;

use App\Contracts\SmsGateway;
use App\Services\Sms\LogSmsGateway;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Swap this binding for a real provider (Twilio, Vonage, Africa's
        // Talking, etc.) once credentials exist — nothing else changes.
        $this->app->bind(SmsGateway::class, LogSmsGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
