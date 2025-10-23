<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = [
        'libelle_fr',
        'libelle_en',
        'abreviation',
        'code',
    ];
}
