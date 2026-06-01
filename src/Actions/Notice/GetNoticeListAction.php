<?php

namespace Ssh521\LaravelAdmin\Actions\Notice;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Ssh521\LaravelAdmin\DTOs\Notice\GetNoticeListDTO;
use Ssh521\LaravelAdmin\Models\AdminNotice;

final class GetNoticeListAction
{
    public function execute(GetNoticeListDTO $dto): LengthAwarePaginator
    {
        return AdminNotice::query()
            ->with(['author', 'attachments'])
            ->when($dto->search, function ($query) use ($dto) {
                $query->where(function ($q) use ($dto) {
                    $q->where('title', 'like', '%'.$dto->search.'%')
                        ->orWhere('content', 'like', '%'.$dto->search.'%');
                });
            })
            ->when($dto->isPublished !== null, function ($query) use ($dto) {
                $query->where('is_published', $dto->isPublished);
            })
            ->orderBy($dto->sortField, $dto->sortDirection)
            ->paginate($dto->perPage);
    }
}
