<?php

namespace Ssh521\LaravelAdmin\Actions\User;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\DTOs\User\GetUserListDTO;
use Ssh521\LaravelAdmin\Support\ResolvesUserModel;

final class GetUserListAction
{
    public function __construct(
        private readonly ResolvesUserModel $users,
    ) {}

    public function execute(GetUserListDTO $dto): LengthAwarePaginator
    {
        return $this->users->query()
            ->when($dto->search, function ($query) use ($dto) {
                $query->where('name', 'like', '%'.$dto->search.'%')
                    ->orWhere('email', 'like', '%'.$dto->search.'%');
            })
            ->orderBy($dto->sortField, $dto->sortDirection)
            ->paginate($dto->perPage);
    }
}
