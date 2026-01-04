<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLastLogin extends Model
{
    use HasFactory, AuditLogTrait;
    protected $table        = 'user_last_login';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_user', 'last_login', 'ip_address'];
    protected $dates        = ['created_at', 'updated_at'];
    protected $casts        = [
        'id_user'           => 'integer',
        'last_login'        => 'datetime',
        'ip_address'        => 'string',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
