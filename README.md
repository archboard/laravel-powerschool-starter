# Laravel PowerSchool Starter

A starting point for new multi-tenant PowerSchool apps by [Archboard](https://archboard.io).

## Features

- **PowerSchool integration.** Install the generated plugin and schools, staff, students, contacts, courses and sections sync automatically, with single sign-on (OpenID Connect) for everyone.
- **Multitenancy** (Spatie). Each district is a tenant, self-hosted or as a managed cloud with signed setup links, district lookup codes, school limits and expiry.
- **Permissions** per school with Bouncer.
- **Activity log** of model changes and notable events, per school.
- **Real-time updates** with Reverb and Echo (for example, sync completion).
- **Multilingual.** English, Chinese, Korean, Japanese, Spanish and Arabic (RTL), with a test that fails when a string is missing a translation.
- **Demo mode** with one-click logins, read-only settings and a scheduled reseed.
- **Deployment** via Docker Compose (Octane on FrankenPHP), a plain VM script, or DigitalOcean App Platform.

## Usage

Start a new project with [degit](https://github.com/Rich-Harris/degit):

```sh
npx degit archboard/laravel-powerschool-starter#main [your project name]
```

Then rename the placeholders: `APP_NAME` in `.env.example` and the `Dockerfile`, the plugin in `storage/plugin`, the named query prefix in `app/SisProviders/PowerSchoolProvider.php`, and the `powerschool-starter` paths and service names in the install scripts.

## Installation

Requirements: PHP 8.5, PostgreSQL, Redis, Node 20+, and a PowerSchool plugin (client ID/secret) for SSO and data sync.

- **[Docker](INSTALLATION.md)** (recommended): a production Compose stack.
- **[VM without Docker](INSTALLATION-VM.md)**: Nginx, PHP-FPM, PostgreSQL, Redis and systemd services on a fresh server.

Either way, finish by visiting `/install` to configure the tenant and create the first administrator.

### Rotating the app key

`APP_KEY` encrypts every tenant's PowerSchool and SMTP credentials, and it signs sessions and cloud setup links. Keep it only in your host's secret store. If it leaks, or on a schedule, rotate it:

1. Generate a new key with `php artisan key:generate --show`. Set it as `APP_KEY`, move the old key to `APP_PREVIOUS_KEYS` (comma-separated), and deploy. Existing secrets, sessions and links keep working.
2. Run `php artisan key:reencrypt` to rewrite the tenant secrets with the new key.
3. Remove `APP_PREVIOUS_KEYS` and deploy. Everyone is signed out, and unused cloud setup links stop working (reissue them with `php artisan tenant:setup-link`).

## Local development

```sh
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
```

Set `APP_URL`, your database login, and placeholder `POWERSCHOOL_ADDRESS`, `POWERSCHOOL_CLIENT_ID` and `POWERSCHOOL_CLIENT_SECRET` values in `.env`, then run `php artisan migrate --seed`. Before running tests, create a `.env.testing` that points at a separate database, because tests erase the database they use.

Seeding creates a tenant on `APP_URL` with an admin (`admin@example.com`), a teacher with a section of students, and a guardian, all with the password `password`. Run `npm run enums` after changing a `#[PublishEnum]` enum to regenerate the JavaScript copies.

| Command | What it does |
| --- | --- |
| `composer test` | Run the Pest suite in parallel |
| `composer analyze` | Run PHPStan |
| `composer format` | Run Pint on changed files |
| `npm run lint` | Run ESLint on `resources/js` |
