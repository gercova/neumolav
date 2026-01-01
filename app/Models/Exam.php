<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'examenes';
    protected $primaryKey   = 'id';
    protected $fillable     = ['dni', 'id_tipo', 'ta', 'fc', 'rf', 'so2', 'peso', 'talla', 'imc', 'pym', 'typ', 'cv', 'abdomen', 'hemolinfopoyetico', 'tcs', 'neurologico', 'hemograma', 'bioquimico', 'perfilhepatico', 'perfilcoagulacion', 'perfilreumatologico', 'orina', 'sangre', 'esputo', 'heces', 'lcr', 'citoquimico', 'adalp', 'paplp', 'bclp', 'cgchlp', 'cbklp', 'bkdab', 'bkcab', 'cgchab', 'papab', 'bcab', 'pulmon', 'pleurabpp', 'fecha_p', 'fecha_s', 'fecha_t', 'funcionpulmonar', 'medicinanuclear', 'plandiag', 'plan', 'otros', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'dni'           => 'string',
        'id_tipo'       => 'integer',
        'ta'            => 'string',
        'fc'            => 'string',
        'rf'            => 'string',
        'so2'           => 'string',
        'peso'          => 'string',
        'talla'         => 'string',
        'imc'           => 'string',
        'pym'           => 'string',
        'typ'           => 'string',
        'cv'            => 'string',
        'abdomen'       => 'string',
        'hemolinfopoyetico' => 'string',
        'tcs'           => 'string',
        'neurologico'   => 'string',
        'hemograma'     => 'string',
        'bioquimico'    => 'string',
        'perfilhepatico'    => 'string',
        'perfilcoagulacion' => 'string',
        'perfilreumatologico' => 'string',
        'orina'         => 'string',
        'sangre'        => 'string',
        'esputo'        => 'string',
        'heces'         => 'string',
        'lcr'           => 'string',
        'citoquimico'   => 'string',
        'adalp'         => 'string',
        'paplp'         => 'string',
        'bclp'          => 'string',
        'cgchlp'        => 'string',
        'cbklp'         => 'string',
        'bkdab'         => 'string',
        'bkcab'         => 'string',
        'cgchab'        => 'string',
        'papab'         => 'string',
        'bcab'          => 'string',
        'pulmon'        => 'string',
        'pleurabpp'     => 'string',
        'fecha_p'       => 'string',
        'fecha_s'       => 'string',
        'fecha_t'       => 'string',
        'funcionpulmonar' => 'string',
        'medicinanuclear' => 'string',
        'plandiag'      => 'string',
        'plan'          => 'string',
        'otros'         => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public static function seePatientByExam($id) {
        return Exam::selectRaw('h.id as history, examenes.id as exam, h.nombres, h.dni')
            ->join('historias as h', 'examenes.dni', '=', 'h.dni')
            ->where('examenes.id', $id)->get();
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'dni');
    }

    public function type(): BelongsTo {
        return $this->belongsTo(ExamType::class, 'id_tipo', 'id');
    }

    public function diagnosis(): BelongsTo {
        return $this->belongsTo(DiagnosticExam::class, 'id_examen', 'id');
    }

    public function medication(): BelongsTo {
        return $this->belongsTo(MedicationExam::class, 'id_examen', 'id');
    }

    public function image(): HasMany {
        return $this->hasMany(Imagen::class, 'id_examen', 'id');
    }
}
