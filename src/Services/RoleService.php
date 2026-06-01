<?php

namespace Ssh521\LaravelAdmin\Services;

use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Actions\Role\GetRoleDetailAction;
use Ssh521\LaravelAdmin\Actions\Role\UpdateRoleAction;
use Ssh521\LaravelAdmin\DTOs\Role\GetRoleDetailDTO;
use Ssh521\LaravelAdmin\DTOs\Role\UpdateRoleDTO;
use Ssh521\LaravelAdmin\Validators\Role\GetRoleDetailValidator;
use Ssh521\LaravelAdmin\Validators\Role\UpdateRoleValidator;

final class RoleService
{
    public function __construct(
        private readonly GetRoleDetailAction $getRoleDetailAction,
        private readonly UpdateRoleAction $updateRoleAction,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function getRoleDetail(array $data): Role
    {
        $validated = GetRoleDetailValidator::validate($data);
        $dto = GetRoleDetailDTO::make($validated);

        return $this->getRoleDetailAction->execute($dto);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateRole(array $data): Role
    {
        $validated = UpdateRoleValidator::validate($data);
        $dto = UpdateRoleDTO::make($validated);

        return $this->updateRoleAction->execute($dto);
    }
}
