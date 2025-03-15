<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anexo extends Model
{
    use HasFactory;

    protected $fillable = ['multa_id', 'arquivo', 'nome_original'];

    public function multa()
    {
        return $this->belongsTo(Multa::class);
    }
}
