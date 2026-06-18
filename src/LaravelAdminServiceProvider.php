<?php

namespace Ssh521\LaravelAdmin;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Console\Commands\InstallCommand;
use Ssh521\LaravelAdmin\Console\Commands\MakeSuperAdminCommand;
use Ssh521\LaravelAdmin\Console\Commands\UpdateCommand;
use Ssh521\LaravelAdmin\Livewire\Admin\HeaderNav;
use Ssh521\LaravelAdmin\Livewire\Admin\LeftMenu;
use Ssh521\LaravelAdmin\Livewire\Admin\LeftMenuMobile;
use Ssh521\LaravelAdmin\Livewire\Admin\MenuCategories\MenuCategoryList;
use Ssh521\LaravelAdmin\Livewire\Admin\Menus\ChangeCategoryModal;
use Ssh521\LaravelAdmin\Livewire\Admin\Menus\MenuOrderModal;
use Ssh521\LaravelAdmin\Livewire\Admin\Roles\RoleDetailModal;
use Ssh521\LaravelAdmin\Livewire\Admin\Roles\RoleForm;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Models\Menu;
use Ssh521\LaravelAdmin\Models\MenuCategory;
use Ssh521\LaravelAdmin\Models\Permission;
use Ssh521\LaravelAdmin\Policies\AdminPolicy;
use Ssh521\LaravelAdmin\Policies\MenuCategoryPolicy;
use Ssh521\LaravelAdmin\Policies\MenuPolicy;
use Ssh521\LaravelAdmin\Policies\PermissionPolicy;
use Ssh521\LaravelAdmin\Policies\RolePolicy;
use Ssh521\LaravelAdmin\Support\AdminMenuRegistrar;
use Ssh521\LaravelAdmin\Support\AdminPackageRegistrar;
use Ssh521\LaravelAdmin\Support\AdminPermissionRegistrar;
use Ssh521\LaravelAdmin\Support\PackageInstaller;

class LaravelAdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-admin.php', 'laravel-admin');
        $this->registerAuthConfiguration();
        $this->registerAdminPackageServices();
        $this->registerAdminUiProvider();
    }

    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerLivewireComponents();
        $this->registerLivewirePersistentMiddleware();
        $this->registerPolicies();
        $this->registerCommands();
        $this->registerPublishes();
    }

    private function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Route::middleware(config('laravel-admin.api_middleware', ['api']))
            ->prefix(config('laravel-admin.api_prefix', 'api/admin'))
            ->group(__DIR__.'/../routes/api.php');
    }

    private function registerAuthConfiguration(): void
    {
        $provider = config('laravel-admin.auth.provider', 'laravel_admin_users');
        $guard = config('laravel-admin.auth.guard', 'laravel_admin');

        config()->set("auth.providers.{$provider}", [
            'driver' => 'eloquent',
            'model' => config('laravel-admin.admin_user_model', AdminUser::class),
        ]);

        config()->set("auth.guards.{$guard}", [
            'driver' => 'session',
            'provider' => $provider,
        ]);
    }

    private function registerAdminPackageServices(): void
    {
        $this->app->singleton(AdminMenuRegistrar::class);
        $this->app->singleton(AdminPermissionRegistrar::class);
        $this->app->singleton(AdminPackageRegistrar::class);
        $this->app->singleton(PackageInstaller::class);
    }

    private function registerAdminUiProvider(): void
    {
        $provider = 'Ssh521\\LaravelAdminUi\\LaravelAdminUiServiceProvider';

        if (class_exists($provider)) {
            $this->app->register($provider);
        }
    }

    private function registerLivewireComponents(): void
    {
        Livewire::component('admin.header-nav', HeaderNav::class);
        Livewire::component('admin.left-menu', LeftMenu::class);
        Livewire::component('admin.left-menu-mobile', LeftMenuMobile::class);
        Livewire::component('admin.menu-categories.menu-category-list', MenuCategoryList::class);
        Livewire::component('admin.menus.menu-order-modal', MenuOrderModal::class);
        Livewire::component('admin.menus.change-category-modal', ChangeCategoryModal::class);
        Livewire::component('admin.roles.role-detail-modal', RoleDetailModal::class);
        Livewire::component('admin.roles.role-form', RoleForm::class);
    }

    private function registerLivewirePersistentMiddleware(): void
    {
        if (! is_callable([Livewire::getFacadeRoot(), 'addPersistentMiddleware'])) {
            return;
        }

        $middleware = config('laravel-admin.middleware', []);
        $middleware = is_array($middleware) ? $middleware : [$middleware];
        $aliases = app('router')->getMiddleware();

        $persistentMiddleware = collect($middleware)
            ->flatten()
            ->filter(fn ($middleware) => is_string($middleware))
            ->map(fn (string $middleware) => Str::before($middleware, ':'))
            ->map(fn (string $middleware) => $aliases[$middleware] ?? $middleware)
            ->filter(fn (string $middleware) => class_exists($middleware))
            ->values()
            ->all();

        if ($persistentMiddleware !== []) {
            Livewire::addPersistentMiddleware($persistentMiddleware);
        }
    }

    private function registerPolicies(): void
    {
        Gate::before(function ($user): ?bool {
            if (method_exists($user, 'hasRole') && $user->hasRole(config('laravel-admin.super_admin_role', 'Super Admin'))) {
                return true;
            }

            return null;
        });

        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(MenuCategory::class, MenuCategoryPolicy::class);
        Gate::policy(Menu::class, MenuPolicy::class);
        Gate::define('viewDashboard', [AdminPolicy::class, 'viewDashboard']);
        Gate::define('viewStatistics', [AdminPolicy::class, 'viewStatistics']);
        Gate::define('manageUsers', [AdminPolicy::class, 'manageUsers']);
        Gate::define('manageRoles', [AdminPolicy::class, 'manageRoles']);
        Gate::define('managePermissions', [AdminPolicy::class, 'managePermissions']);
        Gate::define('manageMenus', [AdminPolicy::class, 'manageMenus']);
        Gate::define('viewLogs', [AdminPolicy::class, 'viewLogs']);
        Gate::define('manageSettings', [AdminPolicy::class, 'manageSettings']);
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            InstallCommand::class,
            MakeSuperAdminCommand::class,
            UpdateCommand::class,
        ]);
    }

    private function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/laravel-admin.php' => config_path('laravel-admin.php'),
        ], 'laravel-admin-config');

        $this->publishes([
            __DIR__.'/../database/migrations/2025_07_22_231822_create_permission_tables.php' => $this->migrationPath('2025_07_22_231822_create_permission_tables.php'),
            __DIR__.'/../database/migrations/2025_07_25_000001_create_menu_system_tables.php' => $this->migrationPath('2025_07_25_000001_create_menu_system_tables.php'),
            __DIR__.'/../database/migrations/2025_09_14_093413_add_sort_order_to_permissions_table.php' => $this->migrationPath('2025_09_14_093413_add_sort_order_to_permissions_table.php'),
            __DIR__.'/../database/migrations/2025_09_14_094345_add_description_to_permissions_table.php' => $this->migrationPath('2025_09_14_094345_add_description_to_permissions_table.php'),
            __DIR__.'/../database/migrations/2026_06_01_000001_create_admin_users_table.php' => $this->migrationPath('2026_06_01_000001_create_admin_users_table.php'),
        ], 'laravel-admin-migrations');

        $this->publishes([
            __DIR__.'/../database/migrations/2026_06_01_000001_create_admin_users_table.php' => $this->migrationPath('2026_06_01_000001_create_admin_users_table.php'),
        ], 'laravel-admin-admin-users-migration');

        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders/vendor/laravel-admin'),
        ], 'laravel-admin-seeders');

    }

    private function migrationPath(string $migrationFile): string
    {
        $migrationName = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $migrationFile);
        $existingMigrations = glob(database_path('migrations/*_'.$migrationName)) ?: [];

        sort($existingMigrations);

        return $existingMigrations[0] ?? database_path('migrations/'.$migrationFile);
    }
}
