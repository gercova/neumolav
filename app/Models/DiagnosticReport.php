<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosticReport extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'informe_diagnostico';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_diagnostico',
        'dni',
        'id_informe',
        'estado',
    ];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_diagnostico'    => 'integer',
        'dni'               => 'string',
        'id_informe'        => 'integer',
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function diagnostic(): BelongsTo {
        return $this->belongsTo(Diagnostic::class, 'id_diagnostico', 'id');
    }

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'dni', 'dni');
    }

    public function report(): BelongsTo {
        return $this->belongsTo(Report::class, 'id_informe', 'id');
    }
}
