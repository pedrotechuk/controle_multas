<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Multa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unidade',
        'data_ciencia',
        'data_multa',
        'data_limite',
        'responsavel',
        'corresponsavel',
        'propriedade',
        'placa',
        'auto_infracao',
        'cod_infracao',
        'condutor',
        'data_identificacao',
        'identificador_interno',
        'data_identificacao_detran',
        'identificador_detran',
        'status',
        'status_final',
        'justificativa',
        'nao_identificacao',
        'nao_desconto',
        'cod_triare',
        'data_finalizada',
        'finalizado_por',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];

    protected $casts = [
        'data_ciencia' => 'datetime',
        'data_multa' => 'datetime',
        'data_limite' => 'datetime',
        'data_identificacao' => 'datetime',
        'data_identificacao_detran' => 'datetime',
    ];

    public function nao_identificado_model()
    {
        return $this->belongsTo(NaoIdentificado::class, 'justificativa', 'id');
    }


    public function nao_descontado_model()
    {
        return $this->belongsTo(NaoDescontado::class, 'id', 'justificativa');
    }

    public function responsavel_model()
    {
        return $this->belongsTo(User::class, 'responsavel', 'name');
    }

    public function infracao()
    {
        return $this->hasOne(Infracao::class, 'cod', 'cod_infracao');
    }

    public function propriedade_model()
    {
        return $this->belongsTo(Propriedade::class, 'propriedade', 'id');
    }

    public function status_model(): HasOne
    {
        return $this->hasOne(Status::class, 'id', 'status');
    }

    public function status_final_model(): HasOne
    {
        return $this->hasOne(StatusFinal::class, 'id', 'status_final');
    }
}
