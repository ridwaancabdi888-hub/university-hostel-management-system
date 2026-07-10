# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in this application, please report it privately rather than opening a public issue. Email **security@your-domain.example** (replace with your real contact before deploying this project) with:

- A description of the vulnerability and its potential impact
- Steps to reproduce it
- Any relevant logs or screenshots

We aim to acknowledge reports within 3 business days.

## Security Measures in Place

- **Authorization**: role-based route middleware plus Laravel Policies (`app/Policies/`) for per-record ownership checks (maintenance requests, visitors, notifications).
- **Rate limiting**: a named limiter (`sensitive-writes`, 30/min per user) applied to finance and approval routes (invoices, payments, visitor approve/reject), in addition to Breeze's built-in login throttling.
- **Security headers**: `app/Http/Middleware/SecurityHeaders.php` sets `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Strict-Transport-Security` (production + HTTPS only), and a Content-Security-Policy on every response.
- **Activity logging**: model changes to Invoices, Payments, Maintenance Requests, Visitors, Room Allocations, Student Profiles, and Users are recorded via `spatie/laravel-activitylog` and viewable by Admins at `/activity-log`. User `password` changes are deliberately excluded from the log.
- **Backups**: automated database + file backups exclude `.env` from the archive; see `config/backup.php`.
- **Session cookies**: `SESSION_SECURE_COOKIE` must be set to `true` in production (see [DEPLOYMENT.md](DEPLOYMENT.md)) so session/CSRF cookies are marked `Secure` over HTTPS.

### Known Trade-off: Content-Security-Policy

The CSP shipped in `SecurityHeaders` allows `'unsafe-inline'` and `'unsafe-eval'` in `script-src`. This is a deliberate, scoped trade-off: the app has a small number of inline `<script>` blocks (a dark-mode toggle in the shared layout, and per-report Chart.js initialization) and uses Alpine.js, which evaluates directive expressions at runtime. Both would break under a strict nonce-based CSP without further work.

The policy still meaningfully blocks:
- Script/style loading from any origin other than `'self'` and the two explicitly trusted CDNs (`cdn.jsdelivr.net` for Chart.js, `fonts.bunny.net` for fonts)
- Framing by other sites (`frame-ancestors 'none'`)
- `<object>`/plugin content and base-tag hijacking

**Follow-up for stricter hardening**: move the inline scripts into Vite-bundled files, generate a per-request nonce via middleware, and switch to Alpine's [CSP-safe build](https://alpinejs.dev/advanced/csp). This was scoped out of the initial production-readiness pass to avoid a broader view-layer refactor, and is tracked here rather than silently accepted.

## Supported Versions

This project does not maintain multiple release branches — security fixes are applied to the `main` branch only.
