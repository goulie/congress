<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewRegistration extends Model
{
    use Translatable;

    protected $table = 'view_registrations';
    protected $fillable = ['congress_id'];
    protected $translatable = ['congress_id'];
}
