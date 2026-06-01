<?php

namespace Ssh521\LaravelAdmin\Actions\AdminUser;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\Actions\AdminUser\Concerns\ResolvesAdminUserModel;
use Ssh521\LaravelAdmin\DTOs\AdminUser\GetAdminUserListDTO;

final class GetAdminUserListAction
{
    use ResolvesAdminUserModel;

    public function execute(GetAdminUserListDTO $dto): LengthAwarePaginator
    {
        $adminUserModel = $this->adminUserModel();

        return $adminUserModel::query()
            ->with(['roles', 'permissions'])
            ->when($dto->search, function ($query) use ($dto): void {
                $query->where(function ($query) use ($dto): void {
                    $query->where('name', 'like', '%'.$dto->search.'%')
                        ->orWhere('email', 'like', '%'.$dto->search.'%');
                });
            })
            ->when($dto->role, function ($query) use ($dto): void {
                $query->whereHas('roles', function ($query) use ($dto): void {
                    $query->where('name', $dto->role)
                        ->where('guard_name', config('laravel-admin.auth.guard', 'laravel_admin'));
                });
            })
            ->orderBy($dto->sortField, $dto->sortDirection)
            ->paginate($dto->perPage);
    }
}
