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
    protected $fillable     = ['dni', 'motivo', 'antecedente', 'sintomas', 'examen_fisico', 'examen_complementario', 'riesgo_neumologico', 'sugerencia', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'dni'                   => 'string',
        'motivo'                => 'string',
        'antecedentes'          => 'string',
        'sintomas'              => 'string',
        'examen_fisico'         => 'string',
        'examen_complementario' => 'string',
        'riesgo_neumologico'    => 'string',
        'sugerencia'            => 'string',
        'estado'                => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public static function seePatientByRisk($id) {
        return Risk::selectRaw('h.id as history, historia_riesgo.id as riesgo, h.nombres, h.dni')
            ->join('historias as h', 'historia_riesgo.dni', '=', 'h.dni')
            ->where('historia_riesgo.id', $id)->get();
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'dni', 'dni');
    }
}
