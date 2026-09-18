<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

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

    public function permissions(): BelongsToMany {
        return $this->belongsToMany(Permission::class, 'module_permission', 'module_id', 'permission_id');
    }
}
