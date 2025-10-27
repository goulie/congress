<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class CategoryParticipant extends Model
{
    use Translatable;

    protected $table = 'category_participants';
    protected $fillable = ['libelle'];
    protected $translatable = ['libelle']; 
}
