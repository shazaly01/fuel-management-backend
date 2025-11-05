<?php

namespace App\Policies;

use App\Models\Truck;
use App\Models\User;

class TruckPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('truck.view');
    }

    public function view(User $user, Truck $truck): bool
    {
        return $user->can('truck.view');
    }

    public function create(User $user): bool
    {
        return $user->can('truck.create');
    }

    public function update(User $user, Truck $truck): bool
    {
        return $user->can('truck.update');
    }

    public function delete(User $user, Truck $truck): bool
    {
        return $user->can('truck.delete');
    }

    public function restore(User $user, Truck $truck): bool
    {
        return $user->can('truck.delete');
    }

    public function forceDelete(User $user, Truck $truck): bool
    {
        return $user->can('truck.delete');
    }
}
