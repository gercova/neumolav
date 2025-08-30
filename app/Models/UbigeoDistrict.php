<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbigeoDistrict extends Model {
    use HasFactory;
    protected $table        = 'ubigeo_districto';
    protected $primaryKey   = 'id';
    protected $keyType      = 'string';
    public $timestamps      = false;
}
