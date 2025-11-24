<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class AgeRange extends Model
{
    use Translatable;

    protected $table = 'age_ranges';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle'];  
}
