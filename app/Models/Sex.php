<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sex extends Model {

    use HasFactory;
    protected $table        = 'sexo';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';

    protected $casts = [
        'id'            => 'string',
        'descripcion'   => 'string',
    ];

    public $timestamps = false;

    public function history(): HasMany {
        return $this->hasMany(History::class, 'id_sexo', 'id');
    }
}
