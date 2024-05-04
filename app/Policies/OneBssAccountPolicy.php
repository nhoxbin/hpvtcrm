<?php

namespace App\Policies;

use App\Models\OneBssAccount;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OneBssAccountPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /* public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Read OneBss');
    } */

    /**
     * Determine whether the user can view the model.
     */
    /* public function view(User $user, OneBssAccount $oneBssAccount): bool
    {
        return $user->hasPermissionTo('Read OneBss');
    } */

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('Login OneBss');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OneBssAccount $oneBssAccount): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OneBssAccount $oneBssAccount): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OneBssAccount $oneBssAccount): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OneBssAccount $oneBssAccount): bool
    {
        //
    }
}
