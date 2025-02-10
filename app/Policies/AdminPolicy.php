<?php

namespace App\Policies;

use App\Classes\Ad;
use App\Models\User;
use App\Models\Permission;

class AdminPolicy
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

        $permission = Permission::where('permission', 'admin.view-any')->where('profile_id', $user->profile_id)->get()->toArray();


        if (count($permission) === 0)
            return false;

        return $permission[0]['value'];
    }
}
