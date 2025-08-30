<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sex extends Model
{
    use HasFactory;
    protected $table        = 'sexo';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';

    public function history(){
        return $this->hasMany(History::class, 'id_sexo', 'id');
    }
}
