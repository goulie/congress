<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Civility extends Model
{
    use Translatable;

    protected $table = 'civilities';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle'];
}
