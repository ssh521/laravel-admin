<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class PackageInstaller
{
    /**
     * @param  iterable<int, string>  $tags
     */
    public function publishTags(Command $command, iterable $tags, bool $force = false): void
    {
        foreach ($tags as $tag) {
            $this->runArtisan($command, 'vendor:publish', [
                '--tag' => $tag,
                '--force' => $force,
            ]);
        }
    }

    public function migrate(Command $command, bool $force = false): void
    {
        $this->runArtisan($command, 'migrate', [
            '--force' => $force,
        ]);
    }

    public function seed(Command $command, string $class, bool $force = false): void
    {
        $this->runArtisan($command, 'db:seed', [
            '--class' => $class,
            '--force' => $force,
        ]);
    }

    /**
     * @param  iterable<int, string>  $tables
     */
    public function hasTables(iterable $tables): bool
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  iterable<int, string>  $columns
     */
    public function hasColumns(string $table, iterable $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  iterable<int, string>  $suffixes
     */
    public function markMigrationFilesAsRanBySuffixes(Command $command, iterable $suffixes): void
    {
        if (! Schema::hasTable('migrations')) {
            return;
        }

        $batch = ((int) DB::table('migrations')->max('batch')) ?: 1;
        $recordedMigrations = DB::table('migrations')->pluck('migration')->all();

        foreach ($this->migrationNamesBySuffixes($suffixes) as $migration) {
            if (in_array($migration, $recordedMigrations, true)) {
                continue;
            }

            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
            ]);

            $command->line("Marked existing migration [{$migration}] as ran.");
        }
    }

    public function canSeedAdminMenus(Command $command, string $packageName): bool
    {
        $hasAdminTables = $this->hasTables([
            'menu_categories',
            'menus',
            'roles',
        ]);

        if (! $hasAdminTables) {
            $command->warn("Laravel Admin tables are missing. Run Laravel Admin migrations before seeding {$packageName} admin menus.");
        }

        return $hasAdminTables;
    }

    /**
     * @param  iterable<int, string>  $requestedPackages
     * @param  array<string, string>  $installers
     * @param  array<string, mixed>  $parameters
     */
    public function installFeaturePackages(
        Command $command,
        iterable $requestedPackages,
        array $installers,
        array $parameters = []
    ): void {
        $supportedValues = implode(', ', array_keys($installers));

        foreach ($requestedPackages as $package) {
            $installerCommand = $installers[$package] ?? null;

            if (! $installerCommand) {
                $command->warn("Unknown Laravel Admin feature package [{$package}]. Supported values: {$supportedValues}.");

                continue;
            }

            if (! array_key_exists($installerCommand, Artisan::all())) {
                $command->warn("Feature installer [{$installerCommand}] is not available.");

                continue;
            }

            $this->runArtisan($command, $installerCommand, $parameters);
        }
    }

    /**
     * @param  array<int, string>|string|null  $values
     * @return array<int, string>
     */
    public function parsePackageList(array|string|null $values): array
    {
        return collect((array) $values)
            ->flatMap(fn (string $value): array => explode(',', $value))
            ->map(fn (string $value): string => trim($value))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  iterable<int, string>  $suffixes
     * @return array<int, string>
     */
    private function migrationNamesBySuffixes(iterable $suffixes): array
    {
        return collect($suffixes)
            ->flatMap(function (string $suffix): array {
                return glob(database_path("migrations/*_{$suffix}.php")) ?: [];
            })
            ->map(fn (string $path): string => pathinfo($path, PATHINFO_FILENAME))
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function runArtisan(Command $command, string $artisanCommand, array $parameters = []): void
    {
        $command->line("Running [php artisan {$artisanCommand}]...");

        $exitCode = Artisan::call($artisanCommand, $parameters);

        $output = trim(Artisan::output());

        if ($output !== '') {
            $command->line($output);
        }

        if ($exitCode !== Command::SUCCESS) {
            throw new RuntimeException("Artisan command [{$artisanCommand}] failed with exit code {$exitCode}.");
        }
    }
}
