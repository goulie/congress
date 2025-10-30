<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tarif extends Model
{
    protected $table = 'tarifs';
    protected $fillable = ['periode_id', 'categorie_registrant_id', 'montant', 'congres_id'];

    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    public function categorie_registrant()
    {
        return $this->belongsTo(CategorieRegistrant::class, 'categorie_registrant_id');
    }

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }
}
