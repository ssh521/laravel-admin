<?php

namespace Ssh521\LaravelAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    protected $signature = 'laravel-admin:install
        {--force : Overwrite published Laravel Admin assets}
        {--skip-vite : Do not update vite.config.js}';

    protected $description = 'Publish Laravel Admin assets and register the Vite input entries.';

    private const VITE_INPUTS = [
        'resources/vendor/laravel-admin/admin.css',
        'resources/vendor/laravel-admin/admin.js',
    ];

    public function handle(): int
    {
        $this->publishAssets();

        if (! $this->option('skip-vite')) {
            $this->updateViteConfig();
        }

        $this->newLine();
        $this->info('Laravel Admin install complete.');
        $this->line('Run [npm run build] for production, or [npm run dev] while developing.');

        return self::SUCCESS;
    }

    private function publishAssets(): void
    {
        $this->line('Publishing Laravel Admin assets...');

        Artisan::call('vendor:publish', [
            '--tag' => 'laravel-admin-assets',
            '--force' => (bool) $this->option('force'),
        ]);

        $output = trim(Artisan::output());

        if ($output !== '') {
            $this->line($output);
        }
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
}
