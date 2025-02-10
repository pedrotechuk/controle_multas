<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusFinal extends Model
{
    use HasFactory;

    protected $fillable = ['status_finals_name', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];
}
