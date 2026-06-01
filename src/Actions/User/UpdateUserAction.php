<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\DTOs\User\UpdateUserDTO;
use Ssh521\LaravelAdmin\Models\User;

final class UpdateUserAction
{
    public function execute(UpdateUserDTO $dto): User
    {
        $user = User::findOrFail($dto->userId);

        $user->name = $dto->name;
        $user->email = $dto->email;

        if ($dto->password !== null) {
            $user->password = Hash::make($dto->password);
        }

        $user->save();

        return $user;
    }
}
