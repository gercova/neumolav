<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosticExam extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'examen_diagnostico';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_examen',
        'id_historia',
        'dni',
        'id_diagnostico',
        'estado'
    ];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function diagnostic(): BelongsTo {
        return $this->belongsTo(Diagnostic::class, 'id_diagnostico', 'id');
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'id_historia', 'id');
    }
}
