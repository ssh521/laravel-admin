<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\CreateAdminUserDTO;

final class CreateAdminUserAction
{
    use ResolvesAdminUserModel;

    public function execute(CreateAdminUserDTO $dto): Model
    {
        $adminUserModel = $this->adminUserModel();
        $adminUser = $adminUserModel::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        if ($dto->isVerified) {
            $adminUser->forceFill(['email_verified_at' => now()])->save();
        }

        return $adminUser;
    }
}
