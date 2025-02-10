<?php

namespace App\Policies;

use App\Classes\Ad;
use App\Models\Permission;
use LdapRecord\Models\ActiveDirectory\User;

class PowerUsersPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    public function viewAny(): bool
    {
        $user = \App\Models\User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.view-any')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.view')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.create')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.update')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.delete')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'power_users.restore')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, Pedido $pedido): bool
    // {
    //     //
    // }
}
