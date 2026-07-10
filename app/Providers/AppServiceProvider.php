<?php

namespace App\Providers;

use App\Contracts\SmsGateway;
use App\Policies\NotificationPolicy;
use App\Services\Sms\LogSmsGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        // DatabaseNotification is a framework class, not an App\Models\*
        // model, so it falls outside Laravel's policy auto-discovery and
        // must be registered explicitly.
        Gate::policy(DatabaseNotification::class, NotificationPolicy::class);

        // Applied to finance/approval write routes (invoices, payments,
        // visitor approve/reject) — see routes/web.php.
        RateLimiter::for('sensitive-writes', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }
}
