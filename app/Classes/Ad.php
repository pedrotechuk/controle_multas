<?php

namespace App\Classes;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Ad
{
    public static function username()
    {
        $user = Auth::user();

        if (!$user) {
            return view('erros.401', []);
        }

        return Auth::user()->samaccountname[0];
    }

    public static function unidade()
    {
        return User::firstWhere('name', self::username())->unidade;
    }
}
