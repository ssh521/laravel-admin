<?php

namespace Ssh521\LaravelAdmin\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Ssh521\LaravelAdmin\Actions\AdminUser\AssignRolesToAdminUserAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\CreateAdminUserAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\DeleteAdminUserAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\GetAdminUserDetailAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\GetAdminUserListAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\SetAdminUserEmailVerificationAction;
use Ssh521\LaravelAdmin\Actions\AdminUser\UpdateAdminUserAction;
use Ssh521\LaravelAdmin\DTOs\AdminUser\AssignRolesToAdminUserDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\CreateAdminUserDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\DeleteAdminUserDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\GetAdminUserDetailDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\GetAdminUserListDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\SetAdminUserEmailVerificationDTO;
use Ssh521\LaravelAdmin\DTOs\AdminUser\UpdateAdminUserDTO;
use Ssh521\LaravelAdmin\Support\AdminBroadcastNotifier;
use Ssh521\LaravelAdmin\Validators\AdminUser\AssignRolesToAdminUserValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\CreateAdminUserValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\DeleteAdminUserValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\GetAdminUserDetailValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\GetAdminUserListValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\SetAdminUserEmailVerificationValidator;
use Ssh521\LaravelAdmin\Validators\AdminUser\UpdateAdminUserValidator;

final class AdminUserService
{
    public function __construct(
        private readonly CreateAdminUserAction $createAdminUserAction,
        private readonly UpdateAdminUserAction $updateAdminUserAction,
        private readonly DeleteAdminUserAction $deleteAdminUserAction,
        private readonly AssignRolesToAdminUserAction $assignRolesToAdminUserAction,
        private readonly GetAdminUserListAction $getAdminUserListAction,
        private readonly GetAdminUserDetailAction $getAdminUserDetailAction,
        private readonly SetAdminUserEmailVerificationAction $setAdminUserEmailVerificationAction,
        private readonly AdminBroadcastNotifier $broadcastNotifier,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function createAdminUser(array $data): Model
    {
        $validated = CreateAdminUserValidator::validate($data);
        $dto = CreateAdminUserDTO::make($validated);

        $adminUser = $this->createAdminUserAction->execute($dto);

        $this->broadcastNotifier->adminUserCreated($adminUser);

        return $adminUser;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateAdminUser(array $data): Model
    {
        $validated = UpdateAdminUserValidator::validate($data);
        $dto = UpdateAdminUserDTO::make($validated);

        $adminUser = $this->updateAdminUserAction->execute($dto);

        $this->broadcastNotifier->adminUserUpdated($adminUser);

        return $adminUser;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function deleteAdminUser(array $data): bool
    {
        $validated = DeleteAdminUserValidator::validate($data);
        $dto = DeleteAdminUserDTO::make($validated);

        $adminUser = $this->getAdminUserDetailAction->execute(new GetAdminUserDetailDTO($dto->adminUserId));
        $deleted = $this->deleteAdminUserAction->execute($dto);

        if ($deleted) {
            $this->broadcastNotifier->adminUserDeleted($adminUser);
        }

        return $deleted;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function assignRolesToAdminUser(array $data): Model
    {
        $validated = AssignRolesToAdminUserValidator::validate($data);
        $dto = AssignRolesToAdminUserDTO::make($validated);

        $adminUser = $this->assignRolesToAdminUserAction->execute($dto);

        $this->broadcastNotifier->adminUserRolesUpdated($adminUser);

        return $adminUser;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getAdminUserList(array $data): LengthAwarePaginator
    {
        $validated = GetAdminUserListValidator::validate($data);
        $dto = GetAdminUserListDTO::make($validated);

        return $this->getAdminUserListAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function getAdminUserDetail(array $data): Model
    {
        $validated = GetAdminUserDetailValidator::validate($data);
        $dto = GetAdminUserDetailDTO::make($validated);

        return $this->getAdminUserDetailAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function setAdminUserEmailVerification(array $data): Model
    {
        $validated = SetAdminUserEmailVerificationValidator::validate($data);
        $dto = SetAdminUserEmailVerificationDTO::make($validated);

        $adminUser = $this->setAdminUserEmailVerificationAction->execute($dto);

        $this->broadcastNotifier->adminUserEmailVerificationUpdated($adminUser, $dto->isVerified);

        return $adminUser;
    }
}
