<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Risk extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'historia_riesgo';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_historia',
        'dni',
        'motivo',
        'antecedente',
        'sintomas',
        'examen_fisico',
        'examen_complementario',
        'riesgo_neumologico',
        'sugerencia',
        'estado'
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts        = [
        'estado'                => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'id_historia', 'id');
    }
}
