<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'module';
    protected $primaryKey   = 'id';
    protected $guarded      = [];
    protected $dates        = ['deleted_at'];

    public static function getSubmodules(int $id) {
        return Submodule::where('module_id', $id)->get();
    }

    public function submodules(): HasMany {
        return $this->hasMany(Submodule::class, 'module_id', 'id');
    }
}
