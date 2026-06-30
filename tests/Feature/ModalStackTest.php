<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Livewire\Livewire;
use Livewire\Component;
use Ssh521\LaravelAdmin\Livewire\Admin\ModalStack;
use Ssh521\LaravelAdmin\Tests\TestCase;

class ModalStackTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Livewire::component('test.modal-child', ModalStackChildStub::class);
    }

    public function test_open_modal_replaces_stack_and_generates_distinct_key_per_open(): void
    {
        $component = Livewire::test(ModalStack::class)
            ->call('openModal', [
                'id' => 'user-edit-a',
                'component' => 'test.modal-child',
                'params' => ['userId' => 1],
                'title' => '사용자 수정',
                'size' => 'md',
            ]);

        $firstKey = $component->get('modals')[0]['key'];

        $component->call('closeModal', 'user-edit-a')
            ->assertSet('modals', [])
            ->call('openModal', [
                'id' => 'user-edit-b',
                'component' => 'test.modal-child',
                'params' => ['userId' => 2],
                'title' => '사용자 수정',
                'size' => 'md',
            ]);

        $modals = $component->get('modals');

        $this->assertCount(1, $modals);
        $this->assertSame(2, $modals[0]['params']['userId']);
        $this->assertNotSame($firstKey, $modals[0]['key']);
    }

    public function test_push_modal_allows_same_component_to_have_isolated_keys(): void
    {
        $component = Livewire::test(ModalStack::class)
            ->call('pushModal', [
                'id' => 'user-edit-a',
                'component' => 'test.modal-child',
                'params' => ['userId' => 1],
                'title' => '사용자 수정',
                'size' => 'md',
            ])
            ->call('pushModal', [
                'id' => 'user-edit-b',
                'component' => 'test.modal-child',
                'params' => ['userId' => 2],
                'title' => '사용자 수정',
                'size' => 'md',
            ]);

        $modals = $component->get('modals');

        $this->assertCount(2, $modals);
        $this->assertNotSame($modals[0]['key'], $modals[1]['key']);
        $this->assertSame(5000, $component->instance()->zIndexFor(0));
        $this->assertSame(5001, $component->instance()->zIndexFor(1));
    }

    public function test_close_top_and_close_all_remove_modal_entries(): void
    {
        Livewire::test(ModalStack::class)
            ->call('pushModal', [
                'id' => 'first',
                'component' => 'test.modal-child',
                'params' => ['userId' => 1],
                'title' => '사용자 수정',
            ])
            ->call('pushModal', [
                'id' => 'second',
                'component' => 'test.modal-child',
                'params' => ['userId' => 2],
                'title' => '사용자 수정',
            ])
            ->call('closeTopModal')
            ->assertSet('modals.0.id', 'first')
            ->call('closeAllModals')
            ->assertSet('modals', []);
    }
}

class ModalStackChildStub extends Component
{
    public int $userId = 0;

    public ?string $modalStackId = null;

    public function render(): string
    {
        return '<div>modal child</div>';
    }
}
