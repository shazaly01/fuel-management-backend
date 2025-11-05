<?php

namespace App\Policies;

use App\Models\OrderStatus;
use App\Models\User;

class OrderStatusPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('order_status.view');
    }

    public function view(User $user, OrderStatus $orderStatus): bool
    {
        return $user->can('order_status.view');
    }

    public function create(User $user): bool
    {
        return $user->can('order_status.create');
    }

    public function update(User $user, OrderStatus $orderStatus): bool
    {
        return $user->can('order_status.update');
    }

    public function delete(User $user, OrderStatus $orderStatus): bool
    {
        return $user->can('order_status.delete');
    }
}
