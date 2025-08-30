<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosticAppointment extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'control_diagnostico';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_control', 'dni', 'id_diagnostico', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_control'        => 'integer',
        'dni'               => 'string',
        'id_diagnostico'    => 'integer',
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function appointment() {
        return $this->belongsTo(Appointment::class, 'id_control', 'id');
    }

    public function diagnostic() {
        return $this->belongsTo(Diagnostic::class, 'id_diagnostico', 'id');
    }

    public function history() {
        return $this->belongsTo(History::class, 'dni', 'dni');
    }
}
