<?php

namespace App\Policies;

use App\Models\FuelOrder;
use App\Models\User;

class FuelOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('fuel_order.view');
    }

    public function view(User $user, FuelOrder $fuelOrder): bool
    {
        return $user->can('fuel_order.view');
    }

    public function create(User $user): bool
    {
        return $user->can('fuel_order.create');
    }

    public function update(User $user, FuelOrder $fuelOrder): bool
    {
        return $user->can('fuel_order.update');
    }

    public function delete(User $user, FuelOrder $fuelOrder): bool
    {
        return $user->can('fuel_order.delete');
    }

    public function restore(User $user, FuelOrder $fuelOrder): bool
    {
        return $user->can('fuel_order.delete');
    }

    public function forceDelete(User $user, FuelOrder $fuelOrder): bool
    {
        return $user->can('fuel_order.delete');
    }
}
