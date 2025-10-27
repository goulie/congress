<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class AccompanyingPersonType extends Model
{
    use Translatable;

    protected $table = 'accompanying_person_types';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle']; 
}
