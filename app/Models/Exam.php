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
    protected $fillable     = [
        'id_historia',
        'dni',
        'id_tipo',
        'ta',
        'fc',
        'rf',
        'so2',
        'peso',
        'talla',
        'imc',
        'pym',
        'typ',
        'cv',
        'abdomen',
        'hemolinfopoyetico',
        'tcs',
        'neurologico',
        'hemograma',
        'bioquimico',
        'perfilhepatico',
        'perfilcoagulacion',
        'perfilreumatologico',
        'orina',
        'sangre',
        'esputo',
        'heces',
        'lcr',
        'citoquimico',
        'adalp',
        'paplp',
        'bclp',
        'cgchlp',
        'cbklp',
        'bkdab',
        'bkcab',
        'cgchab',
        'papab',
        'bcab',
        'pulmon',
        'pleurabpp',
        'fecha_p',
        'fecha_s',
        'fecha_t',
        'funcionpulmonar',
        'medicinanuclear',
        'plandiag',
        'plan',
        'otros',
        'estado'
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts        = [
        'estado'                => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public static function seePatientByExam($id) {
        return Exam::selectRaw('h.id as history, examenes.id as exam, h.nombres, h.dni')
            ->join('historias as h', 'examenes.dni', '=', 'h.dni')
            ->where('examenes.id', $id)->get();
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class);
    }

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function type(): BelongsTo {
        return $this->belongsTo(ExamType::class, );
    }

    public function diagnostics(): HasMany {
        return $this->hasMany(DiagnosticExam::class, 'id_examen', 'id');
    }

    public function medication(): BelongsTo {
        return $this->belongsTo(MedicationExam::class, 'id_examen', 'id');
    }

    public function images(): HasMany {
        return $this->hasMany(Imagen::class, 'id_examen', 'id');
    }
}
