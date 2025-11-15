<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkNature;
use Illuminate\Auth\Access\Response;

class WorkNaturePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('work_nature.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkNature $workNature): bool
    {
        return $user->can('work_nature.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('work_nature.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkNature $workNature): bool
    {
        return $user->can('work_nature.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkNature $workNature): bool
    {
        return $user->can('work_nature.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkNature $workNature): bool
    {
        // عادةً ما تتبع نفس صلاحية الحذف
        return $user->can('work_nature.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkNature $workNature): bool
    {
        // عادةً ما تتبع نفس صلاحية الحذف
        return $user->can('work_nature.delete');
    }
}
