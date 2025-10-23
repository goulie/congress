<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewSessionRegistration extends Model
{
    use Translatable;
    protected $table = 'view_session_registration';
    protected $fillable = ['congress_id'];
    protected $translatable = ['congress_id'];
}
