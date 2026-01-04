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
        'id_drug',
        'descipcion',
        'estado'
    ];

    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'id_control'    => 'integer',
        'dni'           => 'string',
        'id_drug'       => 'integer',
        'descipcion'    => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function history(): HasMany {
        return $this->hasMany(History::class);
    }

    public function appointment(): BelongsTo {
        return $this->belongsTo(Appointment::class);
    }

    public function drug(): HasMany {
        return $this->hasMany(Drug::class, 'id', 'id_drug');
    }
}
