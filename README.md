# Family Eco

![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)
![Vue](https://img.shields.io/badge/Vue-3.x-4FC08D?logo=vue.js&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

Family Eco is a household finance application for managing shared family
finances. Authenticated users can create or join a family with an invite code,
organize income and expense categories, record transactions, and view family
balance and monthly activity from an Inertia-powered dashboard.

The application is a Laravel monolith with a Vue single-page interface. Laravel
handles routing, authentication, validation, authorization, persistence, and
server-side domain operations. Inertia transports server responses to Vue pages
without requiring a separate API layer. Vite Plus builds the TypeScript and Vue
assets and Laravel Wayfinder generates typed frontend route helpers.

## Live demo

Try the project live here:

- https://feco-production-bbd5.up.railway.app/

## Features

- Email/password registration, login, logout, email verification, and password reset
- Google OAuth integration when provider credentials are configured
- Two-factor authentication with confirmation and recovery codes
- Passkey/WebAuthn registration and authentication
- Family creation, invite-code joining, member removal, leaving, and family deletion
- Family-scoped income and expense categories
- Transaction creation with family-owned category validation
- Atomic family balance updates when transactions are recorded
- Dashboard statistics for current-month income, expenses, balance, and recent transactions
- Role and permission checks for family heads and family members
- Profile, password, appearance, two-factor, and passkey settings

## Architecture

```text
Browser
  |
  | Vue 3 + TypeScript + Inertia client
  v
Laravel web routes and Fortify routes
  |
  +-- Controllers / Form Requests
  |     |
  |     +-- Services: family, category, transaction operations
  |     +-- Policies and Spatie roles/permissions
  |
  +-- Eloquent models and migrations
        |
        +-- SQLite by default (configurable through Laravel database settings)
```

The primary aggregate is a `Family`. Users belong to a family, and families own
categories and transactions. `TransactionService` wraps transaction creation
and the corresponding balance mutation in a database transaction and locks the
family row while updating its balance. The dashboard loads the family graph and
calculates current-month totals from transaction timestamps.

### Technology stack

| Area                           | Technology                                                          | Repository evidence                                                       |
| ------------------------------ | ------------------------------------------------------------------- | ------------------------------------------------------------------------- |
| Backend                        | PHP 8.3+, Laravel 13                                                | `composer.json`                                                           |
| Frontend                       | Vue 3, TypeScript, Inertia.js 3                                     | `package.json`, `resources/js`                                            |
| Authentication                 | Laravel Fortify, Socialite, passkeys, two-factor authentication     | `config/fortify.php`, `app/Providers/FortifyServiceProvider.php`          |
| Authorization                  | Spatie Laravel Permission 8                                         | `config/permission.php`, `database/seeders/RolesAndPermissionsSeeder.php` |
| Asset tooling                  | Vite Plus, Vite 8, Laravel Vite, Tailwind CSS 4                     | `vite.config.ts`, `package.json`                                          |
| UI                             | Reka UI, Lucide Vue, VueUse, Vue Sonner, `class-variance-authority` | `package.json`, `resources/js/components`                                 |
| Database                       | SQLite by default; Laravel supports other configured drivers        | `.env.example`, `config/database.php`                                     |
| Testing                        | Pest 5 with PHPUnit and the Pest Laravel plugin                     | `phpunit.xml`, `tests`                                                    |
| Static analysis and formatting | Larastan/PHPStan and Laravel Pint                                   | `phpstan.neon`, `composer.json`                                           |
| CI                             | GitHub Actions on Ubuntu with PHP 8.3 and Node 22                   | `.github/workflows/tests.yml`                                             |

## Project structure

```text
app/
├── Actions/Fortify/          Custom registration and password-reset actions
├── Concerns/                 Shared profile and password validation rules
├── Http/
│   ├── Controllers/          Web, auth, family, transaction, and settings controllers
│   ├── Middleware/           Inertia shared props and appearance handling
│   └── Requests/             Form Request authorization and validation
├── Models/                   User, Family, Category, and Transaction models
├── Policies/                 Category and transaction authorization policies
├── Providers/                Application and Fortify configuration
└── Services/                 Family, category, and transaction domain operations

bootstrap/                    Laravel application bootstrap and middleware aliases
config/                       Application, auth, database, Fortify, permission, and service config
database/
├── factories/                Test data factories
├── migrations/               Users, auth support, families, categories, transactions, and permissions
└── seeders/                  Default user and optional role/domain seeders
resources/
├── css/                      Tailwind and application styles
├── js/
│   ├── actions/              Generated Wayfinder controller helpers
│   ├── components/           Shared UI and dashboard components
│   ├── composables/          Reusable Vue state and behavior
│   ├── layouts/              Application, authentication, and settings layouts
│   ├── pages/                Inertia pages for dashboard, auth, family, and settings
│   ├── routes/               Generated named-route helpers
│   ├── types/                Shared TypeScript types
│   └── wayfinder/            Generated Wayfinder support code
└── views/                    Blade views used where a non-Inertia response is needed
routes/                       Web, settings, and console route definitions
tests/
├── Feature/                  Auth, dashboard, family, category, transaction, and settings tests
└── Unit/                     Unit test suite
public/                       Public entry point and compiled assets
storage/                      Logs, framework cache, sessions, and local application files
```

Generated frontend files under `resources/js/actions`, `resources/js/routes`,
and `resources/js/wayfinder` are ignored by Git and are regenerated by the
Wayfinder Vite plugin during development/builds.

## Prerequisites

- PHP 8.3 or newer with the extensions required by Laravel, including PDO and
  the SQLite driver for the default setup
- Composer 2
- Node.js 22 and npm (the CI workflow uses Node 22)
- A database supported by Laravel; SQLite is the default and requires no server
- A browser with WebAuthn support if passkeys are being tested
- Google OAuth credentials only when Google sign-in is enabled locally

Check the installed toolchain:

```bash
php --version
composer --version
node --version
npm --version
```

## Installation

### Automated setup

The repository defines a Composer setup script that installs PHP and frontend
dependencies, creates `.env` when needed, generates an application key, runs
migrations, and builds frontend assets:

```bash
composer run setup
```

For the default SQLite configuration, create the database file before running
the setup script if it does not already exist:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
composer run setup
```

### Manual setup

```bash
composer install
cp .env.example .env
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate
npm install
npm run build
```

On PowerShell, replace the `cp` command with:

```powershell
Copy-Item .env.example .env
```

The setup script does not overwrite an existing `.env`. Review the generated
file before starting the application.

## Environment configuration

The checked-in `.env.example` is configured for local development with:

- `APP_URL=http://localhost:8000`
- SQLite through `DB_CONNECTION=sqlite`
- Database-backed sessions, cache, and queues
- Log mail delivery through `MAIL_MAILER=log`
- Local file storage and log broadcasting

For a non-SQLite database, set the corresponding `DB_*` values in `.env` and
run the migrations again. Because sessions, cache, and queues use the database
by default, keep the framework migrations current in every environment.

To enable Google OAuth, set these values in `.env` and register the same
callback URL with Google:

```dotenv
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

The configured Fortify security features also use the application URL for
passkey relying-party and origin settings. Use the real HTTPS URL in staging or
production, and do not commit `.env` or provider secrets.

## Running locally

The recommended development command starts the Laravel server, database queue
listener, and Vite dev server concurrently:

```bash
composer run dev
```

The application is normally available at `http://localhost:8000`.

To run the processes separately:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Useful database commands:

```bash
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed
```

`DatabaseSeeder` currently creates a `test@example.com` user with the factory
default password (`password`). The role, family, category, and transaction
seeders exist, but the default seeder does not currently call them; create a
family through the UI or use factories in tests for a complete domain fixture.

## Production build and operation

Build the frontend assets for deployment with:

```bash
npm run build
```

Before serving a production environment, configure a production `.env`, use a
supported database, run migrations, and optimize Laravel configuration:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
npm run build
php artisan queue:work --tries=3
```

Serve the project through a production web server that points its document root
at `public/`. The repository does not include a Docker or Kubernetes deployment
manifest; infrastructure, TLS termination, process supervision, backups, and
secrets management must be supplied by the deployment environment.

## Testing and quality assurance

The test suite uses Pest with PHPUnit configuration in `phpunit.xml`. Tests use
an in-memory SQLite database and isolated array/sync drivers, so they do not
require a running database or queue worker.

Run the complete application test command:

```bash
php artisan test --compact
```

Run focused suites or a single test:

```bash
php artisan test --compact tests/Feature/Auth
php artisan test --compact tests/Feature/TransactionTest.php
php artisan test --compact --filter="family head can delete"
```

Run the project quality checks:

```bash
npm run check
npm run types:check
composer run lint:check
composer run types:check
composer run ci:check
```

`composer ci:check` runs frontend checks, PHPStan/Larastan, and the test suite.
The GitHub Actions workflow runs the same CI check after `composer setup`.
For local automatic PHP formatting, use:

```bash
composer run lint
```

## Route and domain overview

| Area           | Key routes and behavior                                                                                                |
| -------------- | ---------------------------------------------------------------------------------------------------------------------- |
| Public         | `/` renders the welcome page; `/up` is Laravel's health endpoint                                                       |
| Authentication | Fortify registers login, registration, password reset, email verification, two-factor, and passkey flows               |
| Dashboard      | Authenticated users access `/dashboard`; users without a family are redirected to `/family/setup`                      |
| Families       | `/family/setup`, family creation/join/leave/delete, and member removal are protected by authentication and permissions |
| Transactions   | Authenticated users create transactions; viewing the paginated transaction list requires `view-all-transactions`       |
| Categories     | Authenticated users create and delete family categories through policy checks                                          |
| Settings       | Profile, password/security, appearance, two-factor, and passkey management live under `/settings`                      |
| Google OAuth   | `/auth/google` and `/auth/google/callback` handle the Socialite flow                                                   |

## Roles and permissions

`RolesAndPermissionsSeeder` defines the following permissions:

- `manage-family-members`
- `manage-categories`
- `view-all-transactions`
- `create-transaction`
- `edit-own-transaction`
- `delete-own-transaction`
- `manage-budgets`

The `family-head` role receives all defined permissions. The `family-member`
role receives transaction creation and own-transaction edit/delete permissions.
Family membership is represented by `users.family_id`; role assignment is
performed when a user creates or joins a family.

## Contributing

Before opening a change, run the focused tests for the affected behavior and
then the full CI command:

```bash
composer ci:check
```

Keep domain mutations in the existing service layer, validate input with Form
Requests, enforce authorization with policies or permissions, and keep
generated Wayfinder output out of manual edits.

## License

This project is released under the MIT license as declared by the Composer
package metadata.
