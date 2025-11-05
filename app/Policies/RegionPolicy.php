<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\User;

class RegionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('region.view');
    }

    public function view(User $user, Region $region): bool
    {
        return $user->can('region.view');
    }

    public function create(User $user): bool
    {
        return $user->can('region.create');
    }

    public function update(User $user, Region $region): bool
    {
        return $user->can('region.update');
    }

    public function delete(User $user, Region $region): bool
    {
        return $user->can('region.delete');
    }
}
