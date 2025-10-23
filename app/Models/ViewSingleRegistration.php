<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewSingleRegistration extends Model
{
    use Translatable;

    protected $table = 'view_single_registrations';
    protected $fillable = ['congress_id'];
    protected $translatable = ['congress_id'];
}
