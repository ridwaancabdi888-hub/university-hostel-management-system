# University Hostel Management System

A Laravel 12 application for running university hostel operations end to end: room inventory, student registration, room allocation, billing and payments, maintenance requests, visitor management, and a role-based reporting center — with production hardening (authorization policies, security headers, rate limiting, activity logging, and automated backups) built in.

## Features

- **Room Inventory** — hostels, blocks, floors, room types, and rooms with capacity/occupancy tracking.
- **Student Registration** — student profiles, guardian and emergency contact details, document photo upload.
- **Room Allocation** — allocate, transfer, and vacate beds with concurrency-safe locking so two staff members can't double-book the same bed.
- **Billing & Invoicing** — monthly bill generation, late fees, PDF invoices, sequential invoice numbering.
- **Payments** — record payments against invoices, auto-generated receipts (PDF), automatic invoice status sync.
- **Maintenance Requests** — students file tickets, staff triage/assign/resolve, with a full status history and a verification step where the student confirms the fix.
- **Visitor Management** — students register visitors, staff approve or reject, with notifications (database, mail, SMS-ready) at each step.
- **Reports Center** — occupancy, billing, payments, students, and hostel reports, each with charts, PDF export, and Excel export, scoped per role.
- **Role-Based Access Control** — four roles (Admin, Warden, Accountant, Student), enforced through route middleware and Laravel Policies for per-record ownership checks.
- **Activity Log** — an admin-only audit trail of who changed what, powered by `spatie/laravel-activitylog`.
- **Automated Backups** — scheduled database + file backups with health monitoring, powered by `spatie/laravel-backup`.

## Tech Stack

- **Backend**: PHP 8.2, Laravel 12
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **Database**: MySQL
- **PDF/Excel**: `barryvdh/laravel-dompdf`, `maatwebsite/excel`
- **Testing**: PHPUnit (SQLite in-memory for the test suite)

## Getting Started

### Option A — Local (PHP/Composer/Node installed)

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`, then:

```bash
php artisan migrate --seed
npm install
npm run build   # or `npm run dev` for a hot-reloading dev build
php artisan serve
```

Visit `http://localhost:8000`.

### Option B — Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Visit `http://localhost:8000`. See [DEPLOYMENT.md](DEPLOYMENT.md) for the production variant of this setup.

### Demo Accounts

The seeder creates one account per role (password: `password` for all):

| Role       | Email                    |
|------------|--------------------------|
| Admin      | admin@hostel.test        |
| Warden     | warden@hostel.test       |
| Accountant | accountant@hostel.test   |
| Student    | student@hostel.test      |

## Testing

```bash
composer test
# or directly:
php artisan test
```

The suite covers route-level RBAC boundaries, Policy-based ownership rules, the invoice/payment/allocation business logic (including the observers that generate sequential invoice/receipt numbers), and role-gated report access — run entirely against an in-memory SQLite database, no MySQL required.

Code style is enforced with [Laravel Pint](https://laravel.com/docs/pint):

```bash
vendor/bin/pint
```

## Security

See [SECURITY.md](SECURITY.md) for the responsible-disclosure policy and a summary of the security measures in place (CSP, rate limiting, security headers, Policy-based authorization).

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for production `.env` configuration, caching, queue/scheduler setup, and the backup/restore procedure.

## License

Licensed under the [MIT license](LICENSE).
