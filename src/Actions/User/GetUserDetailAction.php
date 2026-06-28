<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Ssh521\LaravelAdmin\DTOs\User\GetUserDetailDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class GetUserDetailAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(GetUserDetailDTO $dto): object
    {
        return $this->users->findOrFail($dto->userId);
    }
}
