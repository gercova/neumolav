<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Imagen extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'imagenes';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'id_examen',
        'id_historia',
        'dni',
        'fecha_examen',
        'imagen',
        'estado'
    ];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_examen'     => 'integer',
        'dni'           => 'string',
        'fecha_examen'  => 'date',
        'imagen'        => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function exam(){
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function history(){
        return $this->hasMany(History::class, 'dni', 'dni');
    }
}
