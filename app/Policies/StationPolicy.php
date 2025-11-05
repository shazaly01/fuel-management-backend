<?php

namespace App\Policies;

use App\Models\Station;
use App\Models\User;

class StationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('station.view');
    }

    public function view(User $user, Station $station): bool
    {
        return $user->can('station.view');
    }

    public function create(User $user): bool
    {
        return $user->can('station.create');
    }

    public function update(User $user, Station $station): bool
    {
        return $user->can('station.update');
    }

    public function delete(User $user, Station $station): bool
    {
        return $user->can('station.delete');
    }

    public function restore(User $user, Station $station): bool
    {
        return $user->can('station.delete');
    }

    public function forceDelete(User $user, Station $station): bool
    {
        return $user->can('station.delete');
    }
}
