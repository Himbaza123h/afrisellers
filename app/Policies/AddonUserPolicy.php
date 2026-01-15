<?php

namespace App\Policies;

use App\Models\AddonUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddonUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any addon users.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the addon user.
     */
    public function view(User $user, AddonUser $addonUser)
    {
        return $user->id === $addonUser->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create addon users.
     */
    public function create(User $user)
    {
        return $user->isVendor() || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the addon user.
     */
    public function update(User $user, AddonUser $addonUser)
    {
        return $user->id === $addonUser->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the addon user.
     */
    public function delete(User $user, AddonUser $addonUser)
    {
        return $user->id === $addonUser->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the addon user.
     */
    public function restore(User $user, AddonUser $addonUser)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the addon user.
     */
    public function forceDelete(User $user, AddonUser $addonUser)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can renew the addon user.
     */
    public function renew(User $user, AddonUser $addonUser)
    {
        return $user->id === $addonUser->user_id;
    }

    /**
     * Determine whether the user can cancel the addon user.
     */
    public function cancel(User $user, AddonUser $addonUser)
    {
        return $user->id === $addonUser->user_id;
    }
}
