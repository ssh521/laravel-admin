<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\DeleteAdminUserDTO;

final class DeleteAdminUserAction
{
    use ResolvesAdminUserModel;

    public function execute(DeleteAdminUserDTO $dto): bool
    {
        $adminUserModel = $this->adminUserModel();
        $adminUser = $adminUserModel::query()->findOrFail($dto->adminUserId);
        $adminUser->delete();

        return true;
    }
}
