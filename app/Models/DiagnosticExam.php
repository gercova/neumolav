<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosticExam extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'examen_diagnostico';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_examen', 'dni', 'id_diagnostico', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_examen'         => 'integer',
        'dni'               => 'string',
        'id_diagnostico'    => 'integer',
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function diagnostic() {
        return $this->belongsTo(Diagnostic::class, 'id_diagnostico', 'id');
    }

    public function history() {
        return $this->belongsTo(History::class, 'dni', 'dni');
    }
}
