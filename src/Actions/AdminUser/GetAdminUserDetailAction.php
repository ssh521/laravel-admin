<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Database\Eloquent\Model;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\GetAdminUserDetailDTO;

final class GetAdminUserDetailAction
{
    use ResolvesAdminUserModel;

    public function execute(GetAdminUserDetailDTO $dto): Model
    {
        $adminUserModel = $this->adminUserModel();

        return $adminUserModel::query()
            ->with(['roles', 'permissions'])
            ->findOrFail($dto->adminUserId);
    }
}
