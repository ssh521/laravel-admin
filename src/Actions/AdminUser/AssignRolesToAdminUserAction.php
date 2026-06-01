<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\AssignRolesToAdminUserDTO;

final class AssignRolesToAdminUserAction
{
    use ResolvesAdminUserModel;

    public function execute(AssignRolesToAdminUserDTO $dto): Model
    {
        $adminUserModel = $this->adminUserModel();
        $adminUser = $adminUserModel::query()->findOrFail($dto->adminUserId);
        $guardName = config('laravel-admin.auth.guard', 'laravel_admin');
        $roles = Role::where('guard_name', $guardName)
            ->whereIn('id', $dto->roleIds)
            ->get();

        $adminUser->syncRoles($roles);

        return $adminUser;
    }
}
