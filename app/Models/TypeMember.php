<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class TypeMember extends Model
{
    use Translatable;

    protected $table = 'type_members';
    protected $fillable = ['libelle', 'congres_id', 'amount', 'currency'];
    protected $translatable = ['libelle'];



}
