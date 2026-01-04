<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'historia_informe';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_historia',
        'dni',
        'antecedentes',
        'historial_enfermedad',
        'examen_fisico',
        'examen_complementario',
        'diagnostico',
        'tratamiento',
        'sugerencia',
        'estado'
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'estado'                => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
    ];

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'dni');
    }

    public function diagnostics(): HasMany {
        return $this->hasMany(DiagnosticReport::class, 'id_informe', 'dni');
    }
}
