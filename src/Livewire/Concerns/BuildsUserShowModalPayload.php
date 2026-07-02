<?php

namespace Ssh521\LaravelAdmin\Livewire\Concerns;

trait BuildsUserShowModalPayload
{
    /**
     * @return array<string, mixed>
     */
    private function userShowModalPayload(int|string $userId): array
    {
        return [
            'id' => 'user-show-'.$userId.'-'.uniqid(),
            'component' => 'admin.users.user-show-modal',
            'params' => ['userId' => $userId],
            'title' => '사용자 상세 정보',
            'size' => 'lg',
            'width' => 680,
            'height' => 480,
            'minHeight' => 420,
        ];
    }
}
