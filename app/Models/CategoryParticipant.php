<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class CategoryParticipant extends Model
{
    use Translatable;

    protected $table = 'category_participants';
    protected $fillable = ['libelle','status'];
    protected $translatable = ['libelle']; 

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'categorie_registrant_id');
    }
}
