<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model {

    use HasFactory, AuditLogTrait;
    protected $table        = 'empresa';
    protected $primaryKey   = 'id';
    protected $fillable     = [
        'razon_social',
        'nombre_comercial',
        'ruc',
        'email',
        'descripcion',
        'frase',
        'mision',
        'vision',
        'ubigeo',
        'iframe_location',
        'direccion',
        'pais',
        'codigo_pais',
        'telefono',
        'pagina_web',
        'representante_legal',
        'foto_representante',
        'logo',
        'logo_mini',
        'logo_receta',
        'rubro',
        'fecha_creacion',
        'autocomplete'
    ];

    protected $dates        = ['created_at', 'updated_at'];
    protected $casts        = [
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime'
    ];
}
