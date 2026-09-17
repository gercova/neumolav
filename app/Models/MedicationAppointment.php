<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationAppointment extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'control_medicacion';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_control',
        'id_historia',
        'dni',
        'id_droga',
        'descripcion',
        'estado'
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'id_control'    => 'integer',
        'id_historia'   => 'integer',
        'dni'           => 'string',
        'id_droga'      => 'integer',
        'descripcion'   => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function history(): BelongsTo {
        return $this->belongsTo(History::class, 'id_historia', 'id');
    }

    public function appointment(): BelongsTo {
        return $this->belongsTo(Appointment::class, 'id_control', 'id');
    }

    public function drug(): BelongsTo {
        return $this->belongsTo(Drug::class, 'id_droga', 'id');
    }
}
