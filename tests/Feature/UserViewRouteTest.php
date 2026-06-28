<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Ssh521\LaravelAdmin\Tests\TestCase;

class UserViewRouteTest extends TestCase
{
    public function test_user_views_do_not_assume_host_home_route(): void
    {
        $viewFiles = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->packagePath('../laravel-admin-ui/resources/views/admin/users'))
        );

        foreach ($viewFiles as $viewFile) {
            if (! $viewFile->isFile() || $viewFile->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($viewFile->getPathname());

            $this->assertStringNotContainsString("route('home')", $source, $viewFile->getPathname());
            $this->assertStringNotContainsString('route("home")', $source, $viewFile->getPathname());
        }
    }
}
