<?php

namespace Ssh521\LaravelAdmin\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\Actions\User\CreateUserAction;
use Ssh521\LaravelAdmin\Actions\User\DeleteUserAction;
use Ssh521\LaravelAdmin\Actions\User\GetUserDetailAction;
use Ssh521\LaravelAdmin\Actions\User\GetUserListAction;
use Ssh521\LaravelAdmin\Actions\User\SetUserEmailVerificationAction;
use Ssh521\LaravelAdmin\Actions\User\UpdateUserAction;
use Ssh521\LaravelAdmin\DTOs\User\CreateUserDTO;
use Ssh521\LaravelAdmin\DTOs\User\DeleteUserDTO;
use Ssh521\LaravelAdmin\DTOs\User\GetUserDetailDTO;
use Ssh521\LaravelAdmin\DTOs\User\GetUserListDTO;
use Ssh521\LaravelAdmin\DTOs\User\SetUserEmailVerificationDTO;
use Ssh521\LaravelAdmin\DTOs\User\UpdateUserDTO;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Validators\User\CreateUserValidator;
use Ssh521\LaravelAdmin\Validators\User\DeleteUserValidator;
use Ssh521\LaravelAdmin\Validators\User\GetUserDetailValidator;
use Ssh521\LaravelAdmin\Validators\User\GetUserListValidator;
use Ssh521\LaravelAdmin\Validators\User\SetUserEmailVerificationValidator;
use Ssh521\LaravelAdmin\Validators\User\UpdateUserValidator;

final class UserService
{
    public function __construct(
        private readonly CreateUserAction $createUserAction,
        private readonly UpdateUserAction $updateUserAction,
        private readonly DeleteUserAction $deleteUserAction,
        private readonly GetUserListAction $getUserListAction,
        private readonly GetUserDetailAction $getUserDetailAction,
        private readonly SetUserEmailVerificationAction $setUserEmailVerificationAction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        $validated = CreateUserValidator::validate($data);
        $dto = CreateUserDTO::make($validated);

        return $this->createUserAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateUser(array $data): User
    {
        $validated = UpdateUserValidator::validate($data);
        $dto = UpdateUserDTO::make($validated);

        return $this->updateUserAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function deleteUser(array $data): bool
    {
        $validated = DeleteUserValidator::validate($data);
        $dto = DeleteUserDTO::make($validated);

        return $this->deleteUserAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getUserList(array $data): LengthAwarePaginator
    {
        $validated = GetUserListValidator::validate($data);
        $dto = GetUserListDTO::make($validated);

        return $this->getUserListAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getUserDetail(array $data): User
    {
        $validated = GetUserDetailValidator::validate($data);
        $dto = GetUserDetailDTO::make($validated);

        return $this->getUserDetailAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function setUserEmailVerification(array $data): User
    {
        $validated = SetUserEmailVerificationValidator::validate($data);
        $dto = SetUserEmailVerificationDTO::make($validated);

        return $this->setUserEmailVerificationAction->execute($dto);
    }
}
