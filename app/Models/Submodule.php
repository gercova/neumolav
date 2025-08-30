<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submodule extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'sub_module';
    protected $primaryKey   = 'id';
    protected $fillable     = ['module_id','descripcion', 'detalle', 'icono'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'module_id'     => 'integer',
        'descripcion'   => 'string',
        'detalle'       => 'string',
        'icono'         => 'string',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function modulo() {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }
}
