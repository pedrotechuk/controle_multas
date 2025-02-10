<?php

namespace App\Policies;

use App\Classes\Ad;
use App\Models\User;
use App\Models\Permission;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.view-any')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }

    public function view(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.view')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }


    public function create(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.create')->where('profile_id', $user->profile_id)->get()->toArray();

        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }


    public function update(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.update')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }


    public function delete(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.delete')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }


    public function restore(): bool
    {
        $user = User::firstWhere('name', Ad::username());

        if (!$user || !$user?->profile_id)
            return false;

        $permission = Permission::where('permission', 'admin.users.restore')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }
}
