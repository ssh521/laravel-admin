<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use PHPUnit\Framework\TestCase;
use Ssh521\LaravelAdmin\Support\HostApplicationTimezone;

class HostApplicationTimezoneTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $path = tempnam(sys_get_temp_dir(), 'laravel-admin-timezone-');
        $this->assertNotFalse($path);
        $this->path = $path;
    }

    protected function tearDown(): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }

        parent::tearDown();
    }

    public function test_it_changes_the_laravel_utc_default_to_asia_seoul(): void
    {
        file_put_contents($this->path, "<?php\n\nreturn [\n    'timezone' => 'UTC',\n];\n");

        $result = (new HostApplicationTimezone)->update($this->path);

        $this->assertSame(HostApplicationTimezone::UPDATED, $result);
        $this->assertStringContainsString("'timezone' => env('APP_TIMEZONE', 'Asia/Seoul'),", (string) file_get_contents($this->path));
    }

    public function test_it_changes_an_env_backed_utc_default_to_asia_seoul(): void
    {
        file_put_contents($this->path, "<?php\n\nreturn [\n    'timezone' => env('APP_TIMEZONE', 'UTC'),\n];\n");

        $result = (new HostApplicationTimezone)->update($this->path);

        $this->assertSame(HostApplicationTimezone::UPDATED, $result);
        $this->assertStringContainsString("'timezone' => env('APP_TIMEZONE', 'Asia/Seoul'),", (string) file_get_contents($this->path));
    }

    public function test_it_is_idempotent_for_the_contract_timezone(): void
    {
        $contents = "<?php\n\nreturn [\n    'timezone' => env('APP_TIMEZONE', 'Asia/Seoul'),\n];\n";
        file_put_contents($this->path, $contents);

        $result = (new HostApplicationTimezone)->update($this->path);

        $this->assertSame(HostApplicationTimezone::CONFIGURED, $result);
        $this->assertSame($contents, file_get_contents($this->path));
    }

    public function test_it_preserves_a_custom_timezone(): void
    {
        $contents = "<?php\n\nreturn [\n    'timezone' => 'Europe/London',\n];\n";
        file_put_contents($this->path, $contents);

        $result = (new HostApplicationTimezone)->update($this->path);

        $this->assertSame(HostApplicationTimezone::CUSTOM, $result);
        $this->assertSame($contents, file_get_contents($this->path));
    }
}
