<?php

namespace Ssh521\LaravelAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Ssh521\LaravelAdmin\Database\Seeders\AdminUserSeeder;
use Ssh521\LaravelAdmin\Database\Seeders\MenuSeeder;
use Ssh521\LaravelAdmin\Database\Seeders\RolePermissionSeeder;

class InstallCommand extends Command
{
    protected $signature = 'laravel-admin:install
        {--force : Overwrite published files and force database commands in production}
        {--skip-migrate : Publish migrations without running them}
        {--skip-seed : Do not seed Laravel Admin roles, menus, or admin user}
        {--skip-admin-user : Do not seed the default admin user}
        {--skip-vite : Do not update vite.config.js}
        {--with=* : Run feature package installers after admin install (bbs, blog)}
        {--with-test-data : Pass sample data option to feature package installers}';

    protected $description = 'Publish, migrate, seed, and configure Laravel Admin resources.';

    private const VITE_INPUTS = [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ];

    public function handle(): int
    {
        $this->info('Installing Laravel Admin...');

        $this->publishResources();

        if (! $this->option('skip-migrate')) {
            $this->runArtisan('migrate', [
                '--force' => (bool) $this->option('force'),
            ]);
        }

        if (! $this->option('skip-seed') && $this->canSeedAdminCore()) {
            $this->seed(RolePermissionSeeder::class);
            $this->seed(MenuSeeder::class);

            if (! $this->option('skip-admin-user')) {
                $this->seed(AdminUserSeeder::class);
            }
        }

        if (! $this->option('skip-vite')) {
            $this->updateViteConfig();
        }

        $this->installFeaturePackages();

        $this->newLine();
        $this->info('Laravel Admin install complete.');
        $this->line('Run [npm run build] for production, or [npm run dev] while developing.');

        return self::SUCCESS;
    }

    private function publishResources(): void
    {
        foreach ([
            'laravel-admin-config',
            'laravel-admin-migrations',
            'laravel-admin-seeders',
            'laravel-admin-views',
            'laravel-admin-components',
            'laravel-admin-assets',
        ] as $tag) {
            $this->runArtisan('vendor:publish', [
                '--tag' => $tag,
                '--force' => (bool) $this->option('force'),
            ]);
        }
    }

    private function seed(string $class): void
    {
        $this->runArtisan('db:seed', [
            '--class' => $class,
            '--force' => (bool) $this->option('force'),
        ]);
    }

    private function canSeedAdminCore(): bool
    {
        $hasAdminTables = Schema::hasTable('permissions')
            && Schema::hasTable('roles')
            && Schema::hasTable('menu_categories')
            && Schema::hasTable('menus');

        if (! $this->option('skip-admin-user')) {
            $hasAdminTables = $hasAdminTables && Schema::hasTable('admin_users');
        }

        if (! $hasAdminTables) {
            $this->warn('Laravel Admin tables are missing. Run migrations before seeding admin core data.');
        }

        return $hasAdminTables;
    }

    private function installFeaturePackages(): void
    {
        $installers = [
            'bbs' => 'laravel-bbs:install',
            'blog' => 'laravel-blog:install',
        ];

        foreach ($this->requestedFeaturePackages() as $package) {
            $command = $installers[$package] ?? null;

            if (! $command) {
                $this->warn("Unknown Laravel Admin feature package [{$package}]. Supported values: bbs, blog.");

                continue;
            }

            if (! array_key_exists($command, Artisan::all())) {
                $this->warn("Feature installer [{$command}] is not available.");

                continue;
            }

            $parameters = [
                '--force' => (bool) $this->option('force'),
                '--skip-migrate' => (bool) $this->option('skip-migrate'),
                '--skip-seed' => (bool) $this->option('skip-seed'),
                '--with-test-data' => (bool) $this->option('with-test-data'),
            ];

            $this->runArtisan($command, $parameters);
        }
    }

    /**
     * @return array<int, string>
     */
    private function requestedFeaturePackages(): array
    {
        return collect((array) $this->option('with'))
            ->flatMap(fn (string $value): array => explode(',', $value))
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function updateViteConfig(): void
    {
        $path = base_path('vite.config.js');

        if (! file_exists($path)) {
            $this->warn('vite.config.js was not found. Add these inputs manually:');
            $this->listViteInputs();

            return;
        }

        $contents = (string) file_get_contents($path);
        $missingInputs = array_values(array_filter(
            self::VITE_INPUTS,
            fn (string $input): bool => ! str_contains($contents, "'{$input}'")
                && ! str_contains($contents, "\"{$input}\"")
        ));

        if ($missingInputs === []) {
            $this->info('Laravel Admin Vite inputs already exist.');

            return;
        }

        $updated = $this->insertInputsIntoViteConfig($contents, $missingInputs);

        if ($updated === null) {
            $this->warn('Could not update vite.config.js automatically. Add these inputs manually:');
            $this->listViteInputs($missingInputs);

            return;
        }

        file_put_contents($path, $updated);

        $this->info('Added Laravel Admin Vite inputs to vite.config.js.');
    }

    /**
     * @param  array<int, string>  $inputs
     */
    private function insertInputsIntoViteConfig(string $contents, array $inputs): ?string
    {
        $pattern = '/input\s*:\s*\[(?<body>.*?)\]/s';

        if (! preg_match($pattern, $contents, $matches, PREG_OFFSET_CAPTURE)) {
            return null;
        }

        $body = $matches['body'][0];
        $bodyStart = $matches['body'][1];
        $indent = $this->detectInputIndent($body);
        $insert = '';

        foreach ($inputs as $input) {
            $insert .= "{$indent}'{$input}',\n";
        }

        $newBody = rtrim($body);

        if ($newBody !== '') {
            if (! str_ends_with(trim($newBody), ',')) {
                $newBody .= ',';
            }

            $newBody .= "\n";
        }

        $newBody .= $insert;

        return substr($contents, 0, $bodyStart)
            .$newBody
            .substr($contents, $bodyStart + strlen($body));
    }

    private function detectInputIndent(string $body): string
    {
        if (preg_match('/\n(?<indent>\s*)[\'"][^\'"]+[\'"]\s*,?/', $body, $matches)) {
            return $matches['indent'];
        }

        return '                ';
    }

    /**
     * @param  array<int, string>|null  $inputs
     */
    private function listViteInputs(?array $inputs = null): void
    {
        foreach ($inputs ?? self::VITE_INPUTS as $input) {
            $this->line("  '{$input}',");
        }
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function runArtisan(string $command, array $parameters = []): void
    {
        $this->line("Running [php artisan {$command}]...");

        $exitCode = Artisan::call($command, $parameters);

        $output = trim(Artisan::output());

        if ($output !== '') {
            $this->line($output);
        }

        if ($exitCode !== self::SUCCESS) {
            throw new RuntimeException("Artisan command [{$command}] failed with exit code {$exitCode}.");
        }
    }
}
