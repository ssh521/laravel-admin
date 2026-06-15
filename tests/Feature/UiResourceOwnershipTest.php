<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Ssh521\LaravelAdmin\LaravelAdminServiceProvider;
use Ssh521\LaravelAdmin\Tests\TestCase;

class UiResourceOwnershipTest extends TestCase
{
    public function test_core_package_no_longer_ships_admin_ui_resource_directories(): void
    {
        $this->assertDirectoryDoesNotExist($this->packagePath('resources'));
        $this->assertDirectoryDoesNotExist($this->packagePath('public/images/dtree'));
    }

    public function test_core_provider_does_not_register_ui_publish_tags(): void
    {
        $this->assertSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-assets'));
        $this->assertSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-views'));
        $this->assertSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-components'));

        $this->assertNotSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-config'));
        $this->assertNotSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-migrations'));
        $this->assertNotSame([], ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-seeders'));
    }

    public function test_core_provider_loads_admin_ui_views_through_ui_package_dependency(): void
    {
        $this->assertFalse(View::exists('admin.index'));
        $this->assertTrue(View::exists('laravel-admin::admin.index'));
        $this->assertTrue(View::exists('laravel-admin::partials.assets'));
    }

    public function test_packaged_admin_controller_views_use_the_admin_ui_namespace(): void
    {
        $views = [
            'laravel-admin::admin.index',
            'laravel-admin::admin.auth.login',
            'laravel-admin::admin.admin-users.index',
            'laravel-admin::admin.menu-categories.index',
            'laravel-admin::admin.menus.index',
            'laravel-admin::admin.permissions.index',
            'laravel-admin::admin.roles.index',
            'laravel-admin::admin.users.index',
            'laravel-admin::livewire.admin.header-nav',
            'laravel-admin::livewire.admin.left-menu',
            'laravel-admin::livewire.admin.users.user-table',
        ];

        foreach ($views as $view) {
            $this->assertTrue(View::exists($view), "Expected view [{$view}] to exist.");
        }
    }
}
