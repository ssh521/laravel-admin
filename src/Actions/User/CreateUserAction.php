<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Support\Facades\Hash;
use Ssh521\LaravelAdmin\DTOs\User\CreateUserDTO;
use Ssh521\LaravelAdmin\Models\User;

final class CreateUserAction
{
    public function execute(CreateUserDTO $dto): User
    {
        $password = $dto->password ?: 'changeme1234!';

        return User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($password),
        ]);
    }
}
