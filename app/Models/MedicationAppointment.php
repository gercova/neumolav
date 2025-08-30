<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationAppointment extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'control_medicacion';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_control', 'dni', 'id_drug', 'descipcion', 'estado'];
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

    public function history(){
        return $this->hasMany(History::class, 'dni', 'dni');
    }

    public function appointment(){
        return $this->belongsTo(Appointment::class, 'id_control', 'id');
    }

    public function drug(){
        return $this->hasMany(Drug::class, 'id', 'id_drug');
    }
}
