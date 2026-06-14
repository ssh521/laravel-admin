<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\DTOs\User\GetUserListDTO;
use Ssh521\LaravelAdmin\Models\User;

final class GetUserListAction
{
    public function execute(GetUserListDTO $dto): LengthAwarePaginator
    {
        return User::query()
            ->when($dto->search, function ($query) use ($dto) {
                $query->where('name', 'like', '%'.$dto->search.'%')
                    ->orWhere('email', 'like', '%'.$dto->search.'%');
            })
            ->orderBy($dto->sortField, $dto->sortDirection)
            ->paginate($dto->perPage);
    }
}
