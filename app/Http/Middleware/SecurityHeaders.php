<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * A pragmatic Content-Security-Policy: it whitelists the one CDN this
     * app still trusts (Chart.js) and allows 'unsafe-inline'/'unsafe-eval'
     * because the app ships a handful of inline <script> blocks (dark-mode
     * toggle, per-report Chart.js init) and uses Alpine.js (which evaluates
     * directive expressions at runtime). A strict nonce-based CSP would
     * require refactoring those views and switching to Alpine's CSP-safe
     * build — tracked as a known follow-up in SECURITY.md rather than
     * silently accepted risk.
     *
     * Fonts (Geist, Material Symbols) are self-hosted under /fonts — no
     * third-party font CDN is needed, so font-src/style-src stay 'self'-only.
     */
    private const CSP = "default-src 'self'; ".
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; ".
        "style-src 'self' 'unsafe-inline'; ".
        "font-src 'self'; ".
        "img-src 'self' data:; ".
        "connect-src 'self'; ".
        "frame-ancestors 'none'; base-uri 'self'; form-action 'self'; object-src 'none'";

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=(), microphone=()');
        $response->headers->set('Content-Security-Policy', self::CSP);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');

        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
