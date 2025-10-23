<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class TypeOrganisation extends Model
{
    use Translatable;

    protected $table = 'type_organisations';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle']; 
}
