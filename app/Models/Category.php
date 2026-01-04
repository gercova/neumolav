<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'droga_categoria';
    protected $primaryKey   = 'id';
    protected $fillable     = ['descripcion', 'detalle', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'descripcion'   => 'string',
        'detalle'       => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function drugs(): HasMany {
        return $this->hasMany(Drug::class, 'id_categoria');
    }
}
