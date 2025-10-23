<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Congress extends Model
{
    use Translatable;

    protected $table = 'congress';
    protected $fillable = ['theme', 'title', 'begin_date', 'end_date', 'host_logo', 'host_name', 'host_country_id', 'invitation_letter_id'];
    protected $translatable = ['theme', 'title'];
}
