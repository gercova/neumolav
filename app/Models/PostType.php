<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostType extends Model
{
    use HasFactory;
    protected $table        = 'post_type';
    protected $primaryKey   = 'id';

    public function posts(): HasMany {
        return $this->hasMany(Post::class, 'type_id', 'id');
    }
}
