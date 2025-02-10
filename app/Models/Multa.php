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
        'propriedade',
        'auto_infracao',
        'condutor',
        'data_identificacao',
        'data_identificacao_detran',
        'status',
        'status_final',
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

    public function propriedade_model()
    {
        return $this->belongsTo(Propriedade::class, 'propriedade', 'id');
    }

    public function local_model(): HasOne
    {
        return $this->hasOne(Propriedade::class, 'id', 'local');
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
