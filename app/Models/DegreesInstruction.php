<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreesInstruction extends Model
{
    use HasFactory;
    protected $table        = 'grado_instruccion';
    protected $primaryKey   = 'id';

    public function history() {
        return $this->hasMany(History::class, 'id_gi', 'id');
    }
}
