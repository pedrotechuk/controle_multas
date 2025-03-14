<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NaoIdentificado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['status_name', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'];

    public function multas()
    {
        return $this->hasMany(Multa::class, 'justificativa', 'id');
    }
}
