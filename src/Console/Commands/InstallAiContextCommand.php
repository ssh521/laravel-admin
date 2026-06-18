<?php

namespace Ssh521\LaravelAdmin\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallAiContextCommand extends Command
{
    protected $signature = 'laravel-admin:install-ai-context
        {--force : Overwrite the existing AI context file}
        {--path=.agents/laravel-admin.md : Project-relative target path for the AI context file}
        {--with-codex-skill : Also install the packaged Codex skill template into the project}
        {--codex-skill-path=.codex/skills/laravel-admin-package : Project-relative target path for the Codex skill template}';

    protected $description = 'Install Laravel Admin AI reference instructions into the host project.';

    public function handle(): int
    {
        $target = $this->targetPath();

        if (File::exists($target) && ! $this->option('force')) {
            $this->warn("AI context file already exists: {$target}");
            $this->line('Use --force to overwrite it.');
        } else {
            File::ensureDirectoryExists(dirname($target));
            File::copy($this->sourcePath(), $target);

            $this->info("Installed Laravel Admin AI context: {$target}");
        }

        if ($this->option('with-codex-skill')) {
            $this->installCodexSkill();
        }

        $this->line('Reference this file from AGENTS.md, Cursor rules, Claude instructions, or GitHub Copilot instructions as needed.');

        return self::SUCCESS;
    }

    private function installCodexSkill(): void
    {
        $target = $this->codexSkillTargetPath();

        if (File::exists($target)) {
            if (! $this->option('force')) {
                $this->warn("Codex skill already exists: {$target}");
                $this->line('Use --force to overwrite it.');

                return;
            }

            File::deleteDirectory($target);
        }

        File::ensureDirectoryExists(dirname($target));
        File::copyDirectory($this->codexSkillSourcePath(), $target);

        $this->info("Installed Laravel Admin Codex skill template: {$target}");
    }

    private function sourcePath(): string
    {
        return dirname(__DIR__, 3).'/stubs/ai/laravel-admin.md';
    }

    private function codexSkillSourcePath(): string
    {
        return dirname(__DIR__, 3).'/stubs/codex-skills/laravel-admin-package';
    }

    private function targetPath(): string
    {
        $path = trim((string) $this->option('path'));

        if ($path === '') {
            $path = '.agents/laravel-admin.md';
        }

        return base_path($path);
    }

    private function codexSkillTargetPath(): string
    {
        $path = trim((string) $this->option('codex-skill-path'));

        if ($path === '') {
            $path = '.codex/skills/laravel-admin-package';
        }

        return base_path($path);
    }
}
