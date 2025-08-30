<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model {
    use HasFactory;
    protected $table        = 'tipo_documento';
    protected $primaryKey   = 'id';

    public function history(){
        return $this->hasMany(History::class, 'id_td', 'id');
    }
}
