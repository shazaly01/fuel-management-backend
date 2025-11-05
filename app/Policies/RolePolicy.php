<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('role.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('role.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('role.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // لا نسمح بتعديل دور "Super Admin" لأي شخص
        if ($role->name === 'Super Admin') {
            return false;
        }

        return $user->can('role.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // --- بداية التعديل ---
        // وظيفة الـ Policy هي فقط التحقق من الصلاحية
        return $user->can('role.delete');
        // --- نهاية التعديل ---
    }


    /**
     * Determine whether the user can restore the model.
     * (غير مستخدم حاليًا لأن موديل Role لا يستخدم SoftDeletes)
     */
    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * (غير مستخدم حاليًا)
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
