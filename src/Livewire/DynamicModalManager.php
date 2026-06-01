<?php

namespace Ssh521\LaravelAdmin\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class DynamicModalManager extends Component
{
    public array $modals = [];

    #[On('dynamic-modal:open')]
    public function openModal(array $data): void
    {
        $keyId = $data['keyId'] ?? null;
        $modalId = $keyId ? "dm-{$keyId}" : 'dm-'.uniqid().'-'.time();

        if ($this->modalExists($modalId)) {
            $this->dispatch('dynamic-modal:bring-to-front', ['modalId' => $modalId]);

            return;
        }

        // 싱글 모달이 열릴 때 기존 싱글 모달 닫기
        $isSingle = ($data['config']['single'] ?? false) === true;
        if ($isSingle) {
            $this->closeExistingSingleModals();
        }

        $this->modals[] = $this->createModalConfig($modalId, $data);
        $this->dispatch('dynamic-modal:add-to-store', ['modal' => end($this->modals)]);
    }

    #[On('dynamic-modal:close')]
    public function closeModal(array $data): void
    {
        $modalId = $data['modalId'] ?? null;

        if (! $modalId || ! $this->modalExists($modalId)) {
            return;
        }

        // 닫히는 모달이 싱글 모달인지 확인
        $closingModal = $this->getModal($modalId);
        $wasSingle = $closingModal && ($closingModal['config']['single'] ?? false) === true;

        $this->modals = array_values(array_filter(
            $this->modals,
            fn ($m) => $m['id'] !== $modalId
        ));

        $this->dispatch('dynamic-modal:remove-from-store', ['modalId' => $modalId]);

        // 싱글 모달이 닫히면 다른 모달들 다시 활성화
        if ($wasSingle) {
            $this->dispatch('dynamic-modal:enable-all-modals');
        }
    }

    #[On('dynamic-modal:close-all')]
    public function closeAll(): void
    {
        // $this->dispatch('dynamic-modal:close-all');
        $this->modals = [];
    }

    public function render()
    {
        return view('livewire.dynamic-modal-manager');
    }

    private function modalExists(string $modalId): bool
    {
        foreach ($this->modals as $modal) {
            if ($modal['id'] === $modalId) {
                return true;
            }
        }

        return false;
    }

    private function createModalConfig(string $modalId, array $data): array
    {
        $defaultConfig = [
            'title' => '제목 없음',
            'width' => 800,
            'height' => 600,
            'minWidth' => 300,
            'minHeight' => 200,
            'resizable' => true,
            'showCloseButton' => true,
            'single' => false,
            'backdrop' => false,
        ];

        // x, y는 명시적으로 전달된 경우에만 포함
        $userConfig = $data['config'] ?? [];
        if (isset($userConfig['x'])) {
            $defaultConfig['x'] = $userConfig['x'];
        }
        if (isset($userConfig['y'])) {
            $defaultConfig['y'] = $userConfig['y'];
        }

        return [
            'id' => $modalId,
            'component' => $data['component'] ?? null,
            'params' => $data['params'] ?? [],
            'config' => array_merge($defaultConfig, $userConfig),
        ];
    }

    private function closeExistingSingleModals(): void
    {
        $singleModals = array_filter($this->modals, fn ($m) => ($m['config']['single'] ?? false) === true);

        foreach ($singleModals as $modal) {
            $this->modals = array_values(array_filter(
                $this->modals,
                fn ($m) => $m['id'] !== $modal['id']
            ));
            $this->dispatch('dynamic-modal:remove-from-store', ['modalId' => $modal['id']]);
        }
    }

    private function getModal(string $modalId): ?array
    {
        foreach ($this->modals as $modal) {
            if ($modal['id'] === $modalId) {
                return $modal;
            }
        }

        return null;
    }
}
