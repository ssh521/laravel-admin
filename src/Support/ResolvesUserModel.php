<?php

namespace Ssh521\LaravelAdmin\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class ResolvesUserModel
{
    public function class(): string
    {
        return config('laravel-admin.user_model', \App\Models\User::class);
    }

    public function query(): Builder
    {
        $class = $this->class();

        return $class::query();
    }

    public function table(): string
    {
        return $this->query()->getModel()->getTable();
    }

    public function findOrFail(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }
}
