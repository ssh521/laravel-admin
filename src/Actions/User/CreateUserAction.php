<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\DTOs\User\CreateUserDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class CreateUserAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(CreateUserDTO $dto): object
    {
        $password = $dto->password ?: 'changeme1234!';

        return $this->users->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($password),
        ]);
    }
}
