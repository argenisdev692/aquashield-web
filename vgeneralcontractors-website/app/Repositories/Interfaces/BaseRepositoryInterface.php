<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    public function create(array $data): Model;
    public function update(Model $model, array $data): Model;
    public function delete(Model $model): bool;
    public function findById(string $id): ?Model;
    public function findByUuid(string $uuid): ?Model;
    public function getAll(array $filters = []): Collection;
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function search(string $term, array $fields = []): Collection;
    public function restore(Model $model): bool;
    public function forceDelete(Model $model): bool;
} 