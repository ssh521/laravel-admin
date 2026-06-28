<?php

namespace Ssh521\LaravelAdmin\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Ssh521\LaravelAdmin\Models\Menu;

class MenuPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return $this->can($user, 'menu-list');
    }

    public function view($user, Menu $menu): bool
    {
        return $this->can($user, 'menu-view');
    }

    public function create($user): bool
    {
        return $this->can($user, 'menu-create');
    }

    public function update($user, Menu $menu): bool
    {
        return $this->can($user, 'menu-edit');
    }

    public function delete($user, Menu $menu): bool
    {
        return $this->can($user, 'menu-delete');
    }

    private function can($user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }
}
