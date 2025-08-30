<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drug extends Model {
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'drogas';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_categoria', 'id_presentacion', 'descripcion', 'detalle', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_categoria'      => 'integer',
        'id_presentacion'   => 'integer',
        'descripcion'       => 'string',
        'detalle'           => 'string',
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function category() {
        return $this->belongsTo(Category::class, 'id_categoria', 'id');
    }

    public function presentation() {
        return $this->belongsTo(Presentation::class, 'id_presentacion', 'id');
    }

    public function diagnosticAppointment() {
        return $this->hasMany(DiagnosticAppointment::class, 'id_droga', 'id');
    }

    public function diagnosticExam() {
        return $this->hasMany(DiagnosticExam::class, 'id_droga', 'id');
    }
}
