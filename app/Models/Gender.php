<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Gender extends Model
{
    use Translatable;

    protected $table = 'genders';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle'];
}
