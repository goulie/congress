<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class StudentLevel extends Model
{
    use Translatable;

    protected $table = 'student_levels';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle'];
}
