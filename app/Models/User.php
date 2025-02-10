<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'profile_id', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];

    public function profile(): HasOne
    {
        return $this->hasOne(related: Profile::class, foreignKey: 'id', localKey: 'profile_id');
    }
}
