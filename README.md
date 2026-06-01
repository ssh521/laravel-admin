# ssh521/laravel-admin

Reusable Laravel 12 admin panel extracted from admin-yaver.

## Install Locally

```bash
composer config repositories.ssh521-laravel-admin path ../packages/laravel-admin
composer require ssh521/laravel-admin:dev-main
php artisan vendor:publish --tag=laravel-admin-config --tag=laravel-admin-assets --no-interaction
php artisan vendor:publish --tag=laravel-admin-migrations --no-interaction
php artisan migrate
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\RolePermissionSeeder"
php artisan db:seed --class="Ssh521\\LaravelAdmin\\Database\\Seeders\\MenuSeeder"
php artisan laravel-admin:make-super-admin admin@example.com --name="Super Admin" --password="change-this-password" --verified
```

The host `App\Models\User` must use `Spatie\Permission\Traits\HasRoles`.

The package admin stylesheet is written for Tailwind CSS v3. If the host app uses the Laravel 12 Tailwind 4 Vite plugin, switch the admin build entry to Tailwind v3/PostCSS or compile the admin CSS in a separate v3 pipeline.

## Initial Super Admin

Run this after migrations and seeders:

```bash
php artisan laravel-admin:make-super-admin admin@example.com --verified
```

If the user exists, the command assigns the `Super Admin` role. If the user does not exist, it creates the user and prompts for a password. For non-interactive setup, pass `--password=...`.

## What Is Included

- Admin layout, left menu, header, dark mode, and modal UI from admin-yaver
- User, role, permission, menu category, menu, notice, and API management screens
- Role-based menu folder access through `MenuCategory` and `menu_category_role`
- Livewire modal components following the existing `admin-users:user-form:open` style events
- Publishable config, migrations, views, and Vite assets

## Additional Development Notes

These are the main areas to revisit while turning the extracted admin panel into a package that can be installed safely in other Laravel projects.

### Package Hardening

- Review every copied view/component for app-specific branding, text, routes, and assets that should move into `config/laravel-admin.php`.
- Add config options for admin route prefix, middleware, default guard, dashboard route, API route prefix, and enabled modules.
- Confirm all package models use package namespaces consistently while still allowing host projects to override the user model.
- Replace any remaining host-app assumptions with configurable values or package-level defaults.
- Add upgrade-safe publish tags for config, assets, migrations, seeders, and views.

### Authentication And Permissions

- Keep `Spatie\Permission\Traits\HasRoles` as a host `User` model requirement and document it in install steps.
- Make Super Admin setup explicit during onboarding through `php artisan laravel-admin:make-super-admin`.
- Verify every Policy keeps the Super Admin bypass and role/menu permission checks aligned.
- Add tests for role-based left menu folder visibility, especially `MenuCategory` access by role.
- Decide whether API access should use Sanctum token abilities, Spatie permissions, or both.

### UI And Assets

- Preserve the current admin UI shape: left menu folders, header, dark mode, modal-based CRUD, and Livewire event naming.
- Keep User management as the reference pattern for future CRUD screens.
- Review modal sizing, mobile behavior, and dark mode after each new admin module.
- Tailwind CSS v3 is currently required for the published admin CSS. If host apps use Tailwind CSS v4, provide either a separate admin CSS build guide or a precompiled asset option.
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
