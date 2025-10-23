<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewAccompagningRegistration extends Model
{
   use Translatable;

   protected $table = 'view_accompagning_registration';
   protected $fillable = ['congress_id'];
   protected $translatable = ['congress_id'];
}
