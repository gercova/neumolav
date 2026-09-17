<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoAtencion extends Model
{
    use HasFactory;

    protected $table = 'tipos_atencion';
    protected $primaryKey = 'id';

    public const NUEVO = 1;
    public const CONTROL = 2;
    public const CONTINUADOR = 3;

    protected $fillable = [
        'descripcion',
        'codigo',
        'badge_color',
    ];

    /**
     * Relationship with Citas
     */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_tipo_atencion', 'id');
    }

    /**
     * Relationship with Historias
     */
    public function histories(): HasMany
    {
        return $this->hasMany(History::class, 'id_tipo_atencion', 'id');
    }
}
