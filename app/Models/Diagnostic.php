<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnostic extends Model {
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'diagnosticos';
    protected $primaryKey   = 'id';
    protected $fillable     = ['descripcion'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'descripcion' => 'string',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function diagnosticAppointment() {
        return $this->hasMany(DiagnosticAppointment::class, 'id_diagnostico', 'id');
    }

    public function diagnosticExam() {
        return $this->hasMany(DiagnosticExam::class, 'id_diagnostico', 'id');
    }

    public function diagnosticReport() {
        return $this->hasMany(DiagnosticReport::class, 'id_diagnostico', 'id');
    }
}
