<?php

namespace Ssh521\LaravelAdmin\Console\Commands;

use Illuminate\Console\Command;
use Ssh521\LaravelAdmin\Support\PackageInstaller;

class UpdateCommand extends Command
{
    protected $signature = 'laravel-admin:update
        {--force : Force database commands and overwrite update-safe published files}
        {--skip-migrate : Publish migrations without running them}
        {--skip-vite : Do not update vite.config.js}
        {--publish-config : Publish Laravel Admin config}
        {--publish-views : Publish Laravel Admin views and components}
        {--publish-seeders : Publish Laravel Admin seeders}
        {--force-config : Overwrite published Laravel Admin config}
        {--force-views : Overwrite published Laravel Admin views and components}
        {--force-seeders : Overwrite published Laravel Admin seeders}
        {--with=* : Run feature package update flow after admin update (bbs, blog, file)}';

    protected $description = 'Update Laravel Admin package resources without overwriting app-owned config or views by default.';

    public function handle(): int
    {
        $this->info('Updating Laravel Admin...');

        $this->publishResources();

        if (! $this->option('skip-migrate')) {
            $this->installer()->migrate($this, (bool) $this->option('force'));
        }

        if (! $this->option('skip-vite')) {
            $this->call('laravel-admin:install', [
                '--skip-migrate' => true,
                '--skip-seed' => true,
                '--skip-admin-user' => true,
                '--with' => [],
            ]);
        }

        $this->updateFeaturePackages();

        $this->newLine();
        $this->info('Laravel Admin update complete.');

        return self::SUCCESS;
    }

    private function publishResources(): void
    {
        $this->installer()->publishTags($this, [
            'laravel-admin-migrations',
            'laravel-admin-ui-assets',
        ], true);

        if ($this->option('publish-config') || $this->option('force-config')) {
            $this->installer()->publishTags($this, ['laravel-admin-config'], (bool) $this->option('force-config'));
        }

        if ($this->option('publish-views') || $this->option('force-views')) {
            $this->installer()->publishTags($this, [
                'laravel-admin-ui-views',
                'laravel-admin-ui-components',
            ], (bool) $this->option('force-views'));
        }

        if ($this->option('publish-seeders') || $this->option('force-seeders')) {
            $this->installer()->publishTags($this, ['laravel-admin-seeders'], (bool) $this->option('force-seeders'));
        }
    }

    private function updateFeaturePackages(): void
    {
        $installers = [
            'bbs' => 'laravel-bbs:install',
            'blog' => 'laravel-blog:install',
            'file' => 'laravel-file:install',
        ];

        $this->installer()->installFeaturePackages($this, $this->requestedFeaturePackages(), $installers, [
            '--force' => (bool) $this->option('force'),
            '--skip-seed' => true,
            '--publish-migrations' => true,
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function requestedFeaturePackages(): array
    {
        return $this->installer()->parsePackageList($this->option('with'));
    }

    private function installer(): PackageInstaller
    {
        return app(PackageInstaller::class);
    }
}
