<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class ViewDashboardRegistration extends Model
{
    use Translatable;

    protected $table = 'view_dashboard_registrations';
    protected $fillable = ['congress_id'];
    protected $translatable = ['congress_id'];
}
