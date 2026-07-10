# Contributing

Thanks for considering a contribution to the University Hostel Management System.

## Getting Set Up

Follow the "Getting Started" section in [README.md](README.md), then run the test suite once to confirm your environment is working:

```bash
php artisan test
```

## Development Workflow

1. Create a branch off `main` for your change.
2. Make your change, following the conventions already established in the codebase (Form Requests for validation, Policies for authorization, Observers for model side-effects — see existing examples before introducing a new pattern).
3. Add or update tests. New business logic or authorization rules should ship with a test — see `tests/Feature` and `tests/Unit/Policies` for the existing shape.
4. Run the checks locally before opening a PR:
   ```bash
   vendor/bin/pint
   php artisan test
   ```
5. Open a pull request using the provided template, filling in the test plan.

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) with its default (`laravel`) preset. Run `vendor/bin/pint` before committing — CI will reject a PR that fails `vendor/bin/pint --test`.

## Reporting Bugs / Requesting Features

Use the issue templates under `.github/ISSUE_TEMPLATE/`. Include which role (Admin/Warden/Accountant/Student) the issue affects — most bugs in this app turn out to be role/authorization-boundary specific.

## Security Issues

Do not open a public issue for a security vulnerability — see [SECURITY.md](SECURITY.md) for the responsible-disclosure process.
