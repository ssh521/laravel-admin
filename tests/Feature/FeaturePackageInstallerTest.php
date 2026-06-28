<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel;
use Ssh521\LaravelAdmin\Support\PackageInstaller;
use Ssh521\LaravelAdmin\Tests\TestCase;

class FeaturePackageInstallerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app[Kernel::class]->registerCommand(new FeaturePackageInstallerDummyCommand);
        $this->app[Kernel::class]->registerCommand(new FeaturePackageInstallerCallerCommand);
    }

    public function test_it_filters_unsupported_options_before_running_feature_package_installers(): void
    {
        FeaturePackageInstallerDummyCommand::$receivedOptions = [];
        FeaturePackageInstallerCallerCommand::$packages = ['bbs'];
        FeaturePackageInstallerCallerCommand::$installers = [
            'bbs' => 'laravel-bbs:install',
        ];
        FeaturePackageInstallerCallerCommand::$parameters = [
            '--force' => true,
            '--skip-migrate' => true,
            '--skip-seed' => true,
            '--with-test-data' => true,
        ];

        $this->artisan('test:feature-package-installer-caller')
            ->assertSuccessful();

        $this->assertTrue(FeaturePackageInstallerDummyCommand::$receivedOptions['force']);
        $this->assertArrayNotHasKey('skip-migrate', FeaturePackageInstallerDummyCommand::$receivedOptions);
        $this->assertArrayNotHasKey('skip-seed', FeaturePackageInstallerDummyCommand::$receivedOptions);
        $this->assertArrayNotHasKey('with-test-data', FeaturePackageInstallerDummyCommand::$receivedOptions);
    }
}

class FeaturePackageInstallerDummyCommand extends Command
{
    protected $signature = 'laravel-bbs:install {--force : Force publish and seed operations}';

    protected $description = 'Dummy Laravel BBS installer for tests.';

    /**
     * @var array<string, mixed>
     */
    public static array $receivedOptions = [];

    public function handle(): int
    {
        self::$receivedOptions = $this->options();

        return self::SUCCESS;
    }
}

class FeaturePackageInstallerCallerCommand extends Command
{
    protected $signature = 'test:feature-package-installer-caller';

    protected $description = 'Runs feature package installer helper for tests.';

    /**
     * @var array<int, string>
     */
    public static array $packages = [];

    /**
     * @var array<string, string>
     */
    public static array $installers = [];

    /**
     * @var array<string, mixed>
     */
    public static array $parameters = [];

    public function handle(PackageInstaller $installer): int
    {
        $installer->installFeaturePackages($this, self::$packages, self::$installers, self::$parameters);

        return self::SUCCESS;
    }
}
