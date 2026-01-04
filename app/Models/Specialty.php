<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialty extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'especialidades';
    protected $fillable     = ['id_ocupacion', 'descripcion', 'detalle', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_ocupacion'  => 'integer',
        'descripcion'   => 'string',
        'detalle'       => 'string',
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function occupation(): BelongsTo {
        return $this->belongsTo(Occupation::class, 'id_ocupacion', 'id');
    }

    public function user(): HasMany {
        return $this->hasMany(User::class, 'id_especialidad', 'id');
    }
}
