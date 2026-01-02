<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'controles';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_historia',
        'dni',
        'sintomas',
        'diagnostico',
        'plan',
        'tratamiento',
        'recomendaciones',
        'estado',
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts        = [
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public static function seePatientByAppointment($id) {
        return Appointment::selectRaw('h.id as history, controles.id as control, h.nombres, h.dni')
            ->join('historias as h', 'controles.dni', '=', 'h.dni')
            ->where('controles.id', $id)->get();
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class);
    }

    public function diagnosticAppointment(): HasMany {
        return $this->hasMany(DiagnosticAppointment::class, 'id_control', 'id');
    }

    public function medicationAppointment(): HasMany {
        return $this->hasMany(MedicationAppointment::class, 'id_control', 'id');
    }
}
