# ssh521/laravel-admin

Reusable Laravel 13 admin panel extracted from admin-yaver.

## Install Locally

```bash
composer config repositories.ssh521-laravel-admin path ../packages/laravel-admin
composer require ssh521/laravel-admin:dev-main
php artisan vendor:publish --tag=laravel-admin-config --no-interaction
php artisan vendor:publish --tag=laravel-admin-migrations --no-interaction
php artisan vendor:publish --tag=laravel-admin-seeders --no-interaction
php artisan laravel-admin:install --force --no-interaction
npm install @fortawesome/fontawesome-free sortablejs axios
npm run build
php artisan migrate
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
```

Admin authentication uses a package-owned `admin_users` table through the configurable `laravel_admin` guard. The host `App\Models\User` is still used for the user management screens; if those screens assign roles to host users, that model must use `Spatie\Permission\Traits\HasRoles`.

`AdminUser` has its own service layer under `Ssh521\LaravelAdmin\Services\AdminUserService`, with matching DTOs, actions, and validators under the `AdminUser` namespace. Use this layer for package-owned administrator accounts; keep `UserService` for host application users shown in the user management screens.

The packaged `AdminUserSeeder` creates or updates the initial admin account and assigns the configured admin role. Override these values in the host app `.env` before seeding:

```dotenv
LARAVEL_ADMIN_SEED_ADMIN_EMAIL=admin@example.com
LARAVEL_ADMIN_SEED_ADMIN_PASSWORD=change-this-password
LARAVEL_ADMIN_SEED_ADMIN_NAME="Super Admin"
LARAVEL_ADMIN_SEED_ADMIN_VERIFIED=true
```

The package admin stylesheet is written for Tailwind CSS v4 and can be included in a Laravel 13 starter kit Vite build. The install command publishes the admin assets and adds these entries to `vite.config.js`:

```js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ],
})
```

## Authentication

The package does not override the host app's `/login`, `/register`, or Fortify views. This avoids route and view conflicts with Breeze, Jetstream, or an existing custom auth stack.

To enable a separate admin login screen at `/admin/login`, set:

```php
'auth' => [
    'guard' => 'laravel_admin',
    'provider' => 'laravel_admin_users',
    'admin_login' => [
        'enabled' => true,
        'middleware' => ['web'],
        'redirect_to' => '/admin',
    ],
],
'admin_user_model' => Ssh521\LaravelAdmin\Models\AdminUser::class,
'admin_users_table' => 'admin_users',
```

Admin routes use the package `AuthenticateAdmin` middleware by default. When admin login is enabled, unauthenticated `/admin` requests are redirected to `/admin/login`; otherwise they fall back to the host app's `login` route. The package registers the configured guard/provider at runtime, so new Laravel projects do not need to edit `config/auth.php` unless they want custom guard names or models.

## Updating Assets

Published CSS/JS files are provided by `ssh521/laravel-admin-ui` and should be overwritten after package updates so the host Vite build uses the latest package assets:

```bash
composer update ssh521/laravel-admin ssh521/laravel-admin-ui
php artisan laravel-admin:install --force --no-interaction
npm install @fortawesome/fontawesome-free sortablejs axios
npm run build
```

Do not force-publish config, migrations, seeders, or views during routine updates. Those files may contain host-app customizations.

## Publishing Seeders

The package seeders can be published when the host app wants to customize the initial roles, permissions, menus, or administrator account:

```bash
php artisan vendor:publish --tag=laravel-admin-seeders --no-interaction
```

Published seeders are copied to:

```text
database/seeders/vendor/laravel-admin
```

The package seeders are also available through their package namespace and can be run without publishing:

```bash
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
```

By default, `MenuSeeder` creates a `관리자 전용` menu folder for administrator account management. This folder is assigned to the `Super Admin` role only; the regular `Admin` role receives the general, member management, and menu management folders, but not the administrator-only folder.

## Feature Package Template

Use [새 기능 패키지 만드는 법](docs/feature-package-template.md) when creating packages such as `laravel-bbs`, `laravel-blog`, or `laravel-file`.

The template covers package naming, ServiceProvider structure, install commands, publish policy, `AdminPackageRegistrar` menu/permission registration, route-level `can:*` middleware, `laravel-admin:install --with=...` integration, and Testbench tests.

## Blade Component Rules

Package-owned admin components live in `ssh521/laravel-admin-ui` under `resources/views/components/admin` and are registered with the `laravel-admin` namespace. Use them as `<x-laravel-admin::admin.primary-button>`, `<x-laravel-admin::admin.layouts.admin>`, and `<x-laravel-admin::admin.input>` inside package views.

Do not add or use unprefixed package components such as `<x-button>`, `<x-input>`, or `<x-layouts.admin>`. Those names belong to the host Laravel application and may already be provided by Breeze, Jetstream, Flux, or custom app components.

Published package views are kept under `resources/views/vendor/laravel-admin`, with components isolated in `resources/views/vendor/laravel-admin/components`. Publish only the components when that is enough:

```bash
php artisan vendor:publish --tag=laravel-admin-ui-components --no-interaction
```

If a host app publishes package views for customization, keep the `x-laravel-admin::` prefix for package components. Only switch to unprefixed `<x-...>` tags when the view intentionally wants to call a host-app component.

If you are updating an existing install that predates the separate `admin_users` table, publish only that new migration:

```bash
php artisan vendor:publish --tag=laravel-admin-admin-users-migration --no-interaction
php artisan migrate
```

## Initial Super Admin

Run the packaged seeder after migrations and role/permission seeding:

```bash
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\AdminUserSeeder"
```

Or create/promote a specific admin account manually:

```bash
php artisan laravel-admin:make-super-admin admin@example.com --verified
```

If the admin user exists, the command assigns the `Super Admin` role. If the admin user does not exist, it creates the account in `admin_users` and prompts for a password. For non-interactive setup, pass `--password=...`.

## What Is Included

- Admin layout, left menu, header, dark mode, and modal UI from admin-yaver through `ssh521/laravel-admin-ui`
- User, role, permission, menu category, menu, and API management screens
- Role-based menu folder access through `MenuCategory` and `menu_category_role`
- Livewire modal components following the existing `admin-users:user-form:open` style events
- Publishable config, migrations, and seeders from this package
- Publishable views and Vite assets from `ssh521/laravel-admin-ui`

## Additional Development Notes

These are the main areas to revisit while turning the extracted admin panel into a package that can be installed safely in other Laravel projects.

### Package Hardening

- Review every copied view/component for app-specific branding, text, routes, and assets that should move into `config/laravel-admin.php`.
- Add config options for admin route prefix, middleware, default guard, dashboard route, API route prefix, and enabled modules.
- Confirm all package models use package namespaces consistently while still allowing host projects to override the user model.
- Replace any remaining host-app assumptions with configurable values or package-level defaults.
- Add upgrade-safe publish tags for config, assets, migrations, seeders, and views.

### Authentication And Permissions

- Keep admin authentication isolated from the host `users` table, while leaving the host `User` model configurable for user management screens.
- Make Super Admin setup explicit during onboarding through `php artisan laravel-admin:make-super-admin`.
- Verify every Policy keeps the Super Admin bypass and role/menu permission checks aligned.
- Add tests for role-based left menu folder visibility, especially `MenuCategory` access by role.
- Decide whether API access should use Sanctum token abilities, Spatie permissions, or both.

### UI And Assets

- Preserve the current admin UI shape: left menu folders, header, dark mode, modal-based CRUD, and Livewire event naming.
- Keep User management as the reference pattern for future CRUD screens.
- Review modal sizing, mobile behavior, and dark mode after each new admin module.
- Keep the published admin CSS compatible with the Laravel 13 Tailwind CSS v4 Vite plugin.
- Check whether published assets should be copied into host `resources/vendor/laravel-admin` or served from package resources in production.

### API Management

- Expand the API management screen beyond token display into token creation, revocation, ability assignment, and usage guidance.
- Add API Resources for new package APIs and keep routes versioned if the surface grows.
- Document recommended Sanctum setup for host projects.
- Add authorization tests for API endpoints, including unauthenticated, authenticated-without-ability, and Super Admin cases.

### Test App

- Keep `testapp` as the local install verification project for Composer path repository testing.
- Use `testapp` to reproduce install-time problems before changing package code.
- Continue checking `composer install`, `npm run build`, `php artisan migrate --seed`, Super Admin creation, register/login views, and basic admin page rendering.
- Add a small smoke test suite in `testapp` for `/register`, `/login`, `/admin`, menu screens, user screens, and API token screens.

### Release Checklist

- Add package-level Pest tests or an orchestra/testbench setup before publishing.
- Confirm migrations and seeders are idempotent enough for repeated local installs.
- Decide the first public version tag and supported Laravel/PHP versions.
- Prepare Packagist metadata, license, and contribution notes before public release.
