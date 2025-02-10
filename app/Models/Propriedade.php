<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Propriedade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['local', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];
}
