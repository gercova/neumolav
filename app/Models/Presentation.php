<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presentation extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'droga_presentacion';
    protected $primaryKey   = 'id';
    protected $fillable     = ['descripcion', 'aka'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'id'            => 'integer',
        'descripcion'   => 'string',
        'aka'           => 'string',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function drug(): HasMany {
        return $this->hasMany(Drug::class, 'id_presentacion', 'id');
    }
}
