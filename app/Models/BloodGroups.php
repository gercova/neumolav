<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodGroups extends Model
{
    use HasFactory;
    protected $table        = 'grupo_sanguineo';
    protected $primaryKey   = 'id';

    public function history() {
        return $this->hasMany(History::class, 'id_gs', 'id');
    }
}
