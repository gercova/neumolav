<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $dates = ['deleted_at'];
    
    protected $table = 'reservations';
    protected $primaryKey = 'id';
}
