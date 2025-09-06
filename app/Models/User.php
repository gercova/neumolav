<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, AuditLogTrait;

    protected $table        = 'users';
    protected $primaryKey   = 'id';
    protected $fillable     = ['name', 'email', 'biografia', 'specialty', 'username', 'avatar', 'id_perfil'];
    protected $hidden       = ['password', 'remember_token'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'name'                  => 'string',
        'email'                 => 'string',
        'biografia'             => 'string',
        'specialty'             => 'string',
        'username'              => 'string',
        'avatar'                => 'string',
        'id_perfil'             => 'integer',
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed'
    ];
    
    protected $appends = ['formatted_name'];

    /*public function getProfilePhotoUrlAttribute() {
        if ($this->avatar) {
            if (filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
            return Storage::exists($this->avatar) ? url($this->avatar) : asset('storage/img/usuarios/default/anonymous.png');
        }
        
        return asset('storage/img/usuarios/default/anonymous.png');
    }*/

    public function getProfilePhotoUrlAttribute() {
        // URL externa
        if ($this->avatar && filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        // Imagen local existe
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::url($this->avatar);
        }

        // Imagen por defecto
        return asset('storage/img/usuarios/default/anonymous.png');
    }

    public function getFormattedNameAttribute() {
        if (empty($this->name)) return '';

        $parts = array_filter(explode(' ', $this->name));
        
        switch (count($parts)) {
            case 0:
                return '';
            case 1:
                return $this->name;
            case 2:
            case 3:
                return $this->name;
            default:
                $firstName      = $parts[0];
                $middleInitial  = strlen($parts[1]) > 0 ? substr($parts[1], 0, 1) . '.' : '';
                $lastName       = $parts[count($parts) - 2];
                return trim("{$firstName} {$middleInitial} {$lastName}");
        }
    }

    public static function getUserByRole($userId) {
        return DB::table('model_has_roles')->where('model_id', $userId)->get();
    }

    public function profile() {
        return $this->hasOne(Profile::class, 'id_perfil', 'id');
    }

    public function lastLogin() {
        return $this->hasOne(UserLastLogin::class, 'id_user', 'id');
    }

    public function specialty() {
        return $this->hasOne(Specialty::class, 'id_especialidad', 'id');
    }
}