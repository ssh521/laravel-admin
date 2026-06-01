<?php

namespace Ssh521\LaravelAdmin\Livewire\Admin;

use Livewire\Attributes\On;
use Livewire\Component;

class LeftMenuMobile extends Component
{
    #[On('admin-menus:menu-order:updated')]
    public function refreshMenuOrder()
    {
        // 메뉴 순서가 변경되었을 때 left-menu 컴포넌트가 자동으로 새로고침됨
        // 이 컴포넌트는 left-menu를 포함하고 있으므로 별도 처리 불필요
    }

    public function render()
    {
        return view('livewire.admin.left-menu-mobile');
    }
}
