<?php

namespace App\Policies;

use App\Models\DigiShop;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DigiShopPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('Read DigiShop');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DigiShop $digiShop): bool
    {
        return $user->hasPermissionTo('Read DigiShop');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DigiShop $digiShop): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DigiShop $digiShop): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DigiShop $digiShop): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DigiShop $digiShop): bool
    {
        //
    }
}
