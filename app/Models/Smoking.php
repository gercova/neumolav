<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Smoking extends Model
{
    use HasFactory;
    protected $table        = 'tabaquismo';
    protected $primaryKey   = 'id';

    public function history() {
        return $this->hasMany(History::class, 'id_ct', 'id');
    }
}
