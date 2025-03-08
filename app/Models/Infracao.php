<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infracao extends Model
{
    use HasFactory;

    protected $fillable = ['cod', 'cod_infracao', 'responsavel', 'valor', 'orgao_atuador', 'art_ctb', 'pontos', 'gravidade'];

    public function multa()
    {
        return $this->belongsTo(Multa::class, 'cod', 'cod_infracao');
    }
}
