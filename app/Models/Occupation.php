<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occupation extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'ocupaciones';
    protected $primaryKey   = 'id';
    protected $fillable     = ['descripcion', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'descripcion'   => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function history(): HasMany {
        return $this->hasMany(History::class, 'dni', 'dni');
    }
}
