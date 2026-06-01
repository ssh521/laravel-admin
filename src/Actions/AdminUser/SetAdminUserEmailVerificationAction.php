<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Database\Eloquent\Model;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\SetAdminUserEmailVerificationDTO;

final class SetAdminUserEmailVerificationAction
{
    use ResolvesAdminUserModel;

    public function execute(SetAdminUserEmailVerificationDTO $dto): Model
    {
        $adminUserModel = $this->adminUserModel();
        $adminUser = $adminUserModel::query()->findOrFail($dto->adminUserId);

        $adminUser->forceFill([
            'email_verified_at' => $dto->isVerified ? now() : null,
        ])->save();

        return $adminUser;
    }
}
