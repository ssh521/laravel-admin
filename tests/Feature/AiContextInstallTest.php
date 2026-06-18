<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Ssh521\LaravelAdmin\LaravelAdminServiceProvider;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AiContextInstallTest extends TestCase
{
    public function test_ai_context_publish_tag_is_registered(): void
    {
        $paths = ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-ai-context');

        $this->assertNotSame([], $paths);
        $this->assertContains(base_path('.agents/laravel-admin.md'), $paths);
    }

    public function test_codex_skill_publish_tag_is_registered(): void
    {
        $paths = ServiceProvider::pathsToPublish(LaravelAdminServiceProvider::class, 'laravel-admin-codex-skill');

        $this->assertNotSame([], $paths);
        $this->assertContains(base_path('.codex/skills/laravel-admin-package'), $paths);
    }

    public function test_install_ai_context_command_copies_context_file(): void
    {
        $target = base_path('.agents/laravel-admin.md');

        File::delete($target);

        $exitCode = Artisan::call('laravel-admin:install-ai-context');

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($target);
        $this->assertStringContainsString('Laravel Admin AI Context', File::get($target));
        $this->assertStringContainsString('admin-ui-design-contract.md', File::get($target));
    }

    public function test_install_ai_context_command_does_not_overwrite_without_force(): void
    {
        $target = base_path('.agents/laravel-admin.md');

        File::ensureDirectoryExists(dirname($target));
        File::put($target, 'custom instructions');

        $exitCode = Artisan::call('laravel-admin:install-ai-context');

        $this->assertSame(0, $exitCode);
        $this->assertSame('custom instructions', File::get($target));
    }

    public function test_install_ai_context_command_can_install_codex_skill_template(): void
    {
        $target = base_path('.codex/skills/laravel-admin-package');

        File::deleteDirectory($target);

        $exitCode = Artisan::call('laravel-admin:install-ai-context', [
            '--with-codex-skill' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($target.'/SKILL.md');
        $this->assertStringContainsString('laravel-admin-package', File::get($target.'/SKILL.md'));
        $this->assertFileExists($target.'/agents/openai.yaml');
    }
}
