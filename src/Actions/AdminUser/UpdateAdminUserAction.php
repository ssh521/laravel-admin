<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\UpdateAdminUserDTO;

final class UpdateAdminUserAction
{
    use ResolvesAdminUserModel;

    public function execute(UpdateAdminUserDTO $dto): Model
    {
        $adminUserModel = $this->adminUserModel();
        $adminUser = $adminUserModel::query()->findOrFail($dto->adminUserId);

        $adminUser->name = $dto->name;
        $adminUser->email = $dto->email;

        if ($dto->password !== null) {
            $adminUser->password = Hash::make($dto->password);
        }

        $adminUser->save();

        return $adminUser;
    }
}
