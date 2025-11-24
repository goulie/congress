<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class SiteVisite extends Model
{
    use Translatable;
    protected $table = 'site_visites';
    protected $fillable = ['libelle','congres_id','amount'];
    protected $translatable = ['libelle'];  
    
    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'site_visit_id');
    }
}
