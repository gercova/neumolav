<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;
    protected $table        = 'tipo_examen';
    protected $primaryKey   = 'id';
    
    public function exams() {
        return $this->hasMany(Exam::class, 'id_tipo', 'id');
    }
}
