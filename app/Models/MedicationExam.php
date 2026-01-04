<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationExam extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'examen_medicacion';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_examen', 'id_historia', 'dni', 'id_droga', 'descripcion', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_examen'     => 'integer',
        'dni'           => 'string',
        'id_droga'      => 'integer',
        'descripcion'   => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime'
    ];

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'id_historia', 'id');
    }

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function drug(): BelongsTo {
        return $this->belongsTo(Drug::class, 'id_droga', 'id');
    }
}
