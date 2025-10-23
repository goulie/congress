<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewGroupRegistration extends Model
{
   use Translatable; 

    protected $table = 'view_group_registrations';
        protected $fillable = ['congress_id'];
    protected $translatable = ['congress_id'];
}
