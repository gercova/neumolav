<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaritalStatus extends Model
{
    use HasFactory;
    protected $table        = 'estado_civil';
    protected $primaryKey   = 'id';

    public function history(): HasMany {
        return $this->hasMany(History::class, 'id_estado', 'id');
    }
}
