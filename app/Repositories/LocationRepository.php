<?php

namespace App\Repositories;

use App\Models\Location;
use Illuminate\Contracts\Pagination\Paginator;

class LocationRepository implements ILocationRepository
{

    public function getById(int $id): Location
    {
        return Location::findOrFail($id);
    }

    public function paginate(int $count): Paginator
    {
        return Location::paginate($count);
    }

    public function create(array $data): Location
    {
        return Location::create($data);
    }

    public function update(int $id, array $data): Location
    {
        $location = $this->getById($id);
        $location->update($data);

        return $location;
    }

    public function delete(int $id): void
    {
        $location = $this->getById($id);
        $location->delete();
    }
}
