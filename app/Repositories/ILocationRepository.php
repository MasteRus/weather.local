<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Contracts\Pagination\Paginator;

interface ILocationRepository
{
    public function getById(int $id): Location;

    public function paginate(int $count): Paginator;

    public function create(array $data): Location;

    public function update(int $id, array $data): Location;

    public function delete(int $id): void;
}
