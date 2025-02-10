<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'active', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];
}
