<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\DeleteUserDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class DeleteUserAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(DeleteUserDTO $dto): bool
    {
        $user = $this->users->findOrFail($dto->userId);
        $user->delete();

        return true;
    }
}
