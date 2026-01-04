<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'perfiles';
    protected $primaryKey   = 'id';
    protected $fillable     = ['perfil', 'estado'];
    public $timestamps      = false;
    protected $casts = [
        'id'            => 'integer',
        'perfil'        => 'string',
        'estado'        => 'boolean',
    ];

    public function users(): HasMany {
        return $this->hasMany(User::class, 'id_perfil', 'id');
    }
}
