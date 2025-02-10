<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = ['permission', 'profile_id', 'value', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'] ;
}
