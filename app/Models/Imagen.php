<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'estado'        => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class, 'id_examen', 'id');
    }

    public function history(): HasMany {
        return $this->hasMany(History::class, 'id_historia', 'dni');
    }
}
