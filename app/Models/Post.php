<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model {
    use HasFactory, SoftDeletes;
    protected $table        = 'post';
    protected $primaryKey   = 'id';
    protected $fillable     = ['type_id', 'titulo', 'titulo_corto', 'url', 'img', 'descrip_img', 'alt_img', 'resumen', 'contenido', 'categories', 'meta_content', 'key_words', 'etiquetas', 'autor_post'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'type_id'       => 'integer',
        'titulo'        => 'string',
        'titulo_corto'  => 'string',
        'url'           => 'string',
        'img'           => 'string',
        'descrip_img'   => 'string',
        'alt_img'       => 'string',
        'resumen'       => 'string',
        'contenido'     => 'string',
        'categories'    => 'string',
        'meta_content'  => 'string',
        'key_words'     => 'string',
        'etiquetas'     => 'string',
        'user_id'       => 'integer',
        'estado'        => 'boolean',
        'autor_post'    => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($post) {
            $post->url = Str::slug($post->titulo); // Convierte "Este es un título" -> "este-es-un-titulo"
        });
    }

    /*public function setCreatedAtAttribute($value){
        $this->attributes['created_at'] = Carbon::createFromFormat('F j, Y', $value);
    }*/

    public function user(){
        return $this->belongsTo(User::class, 'autor_post', 'id');
    }

    public function postType(){
        return $this->belongsTo(PostType::class, 'type_id', 'id');
    }
}
