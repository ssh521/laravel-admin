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
        $this->assertTrue(View::exists('admin.index'));
        $this->assertTrue(View::exists('laravel-admin::partials.assets'));
    }
}
