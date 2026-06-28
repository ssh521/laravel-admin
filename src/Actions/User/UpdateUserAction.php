<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\DTOs\User\UpdateUserDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class UpdateUserAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(UpdateUserDTO $dto): object
    {
        $user = $this->users->findOrFail($dto->userId);

        $user->name = $dto->name;
        $user->email = $dto->email;

        if ($dto->password !== null) {
            $user->password = Hash::make($dto->password);
        }

        $user->save();

        return $user;
    }
}
