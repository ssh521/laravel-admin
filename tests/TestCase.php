<?php

namespace Ssh521\LaravelAdmin\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\PermissionServiceProvider;
use Ssh521\LaravelAdmin\Database\Seeders\RolePermissionSeeder;
use Ssh521\LaravelAdmin\LaravelAdminServiceProvider;
use Ssh521\LaravelAdmin\Models\Permission;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--path' => $this->packagePath('database/migrations'),
            '--realpath' => true,
        ])->run();

        $this->seed(RolePermissionSeeder::class);
    }

    /**
     * @param  mixed  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PermissionServiceProvider::class,
            LivewireServiceProvider::class,
            LaravelAdminServiceProvider::class,
        ];
    }

    /**
     * @param  mixed  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('cache.default', 'array');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('permission.cache.store', 'array');
        $app['config']->set('permission.models.permission', Permission::class);
    }

    protected function packagePath(string $path = ''): string
    {
        $basePath = dirname(__DIR__);

        return $path === '' ? $basePath : $basePath.'/'.$path;
    }
}
