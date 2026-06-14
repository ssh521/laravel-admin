<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Livewire\Livewire;
use Ssh521\LaravelAdmin\Http\Middleware\AuthenticateAdmin;
use Ssh521\LaravelAdmin\Tests\TestCase;

class LivewireMiddlewareTest extends TestCase
{
    public function test_it_registers_admin_authentication_as_livewire_persistent_middleware(): void
    {
        $this->assertContains(AuthenticateAdmin::class, Livewire::getPersistentMiddleware());
    }
}
