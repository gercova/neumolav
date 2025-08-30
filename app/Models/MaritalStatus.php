<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    use HasFactory;
    protected $table        = 'estado_civil';
    protected $primaryKey   = 'id';

    public function history(){
        return $this->hasMany(History::class, 'id_estado', 'id');
    }
}
