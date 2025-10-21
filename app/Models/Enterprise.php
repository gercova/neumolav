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
        'id'                => 'integer',
       'razon_social'       => 'integer',
        'nombre_comercial'  => 'integer', 
        'ruc'               => 'integer',
        'email'             => 'integer',
        'descripcion'       => 'integer', 
        'frase'             => 'integer',
        'mision'            => 'integer',
        'vision'            => 'integer',
        'ubigeo'            => 'integer',
        'iframe_location'   => 'integer',
        'direccion'         => 'integer',
        'pais'              => 'integer',
        'codigo_pais'       => 'integer',
        'telefono'          => 'integer',
        'pagina_web'        => 'integer',
        'representante_legal' => 'integer',
        'foto_representante' => 'integer',
        'logo'              => 'integer',
        'logo_mini'         => 'integer',
        'logo_receta'       => 'integer',
        'rubro'             => 'integer',
        'fecha_creacion'    => 'integer',
        'autocomplete'      => 'integer',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime'
    ];
}
