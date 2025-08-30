<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationExam extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'examen_medicacion';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_examen', 'dni', 'id_droga', 'descripcion', 'estado'];
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

    public function history(){
        return $this->belongsTo(History::class, 'dni', 'dni');
    }

    public function exam(){
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function drug(){
        return $this->belongsTo(Drug::class, 'id_droga', 'id');
    }
}