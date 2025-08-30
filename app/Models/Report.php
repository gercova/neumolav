<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model {
    
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'historia_informe';
    protected $primaryKey   = 'id';
    protected $fillable     = ['dni', 'antecedentes', 'historial_enfermedad', 'examen_fisico', 'examen_complementario', 'diagnostico', 'tratamiento', 'sugerencia', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'                    => 'integer',
        'dni'                   => 'string',
        'antecedentes'          => 'string',
        'historial_enfermedad'  => 'string',
        'examen_fisico'         => 'string',
        'examen_complementario'  => 'string',
        'diagnostico'           => 'string',
        'tratamiento'           => 'string',
        'sugerencia'            => 'string',
        'estado'                => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public static function seePatientByReport($id) {
        return Report::selectRaw('h.id as history, historia_informe.id as reporte, h.nombres, h.dni')
            ->join('historias as h', 'historia_informe.dni', '=', 'h.dni')
            ->where('historia_informe.id', $id)->get();
    }

    public function history() {
        return $this->belongsTo(History::class, 'dni');
    }

    public function diagnosticReport() {
        return $this->hasMany(DiagnosticReport::class, 'id_informe', 'dni');
    }
}
