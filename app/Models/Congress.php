<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Congress extends Model
{
    use Translatable;

    protected $table = 'congress';
    protected $fillable = [
        'theme',
        'title',
        'begin_date',
        'end_date',
        'host_logo',
        'host_name',
        'host_country_id',
        'invitation_letter_id',
        'amount_visit',
        'amount_diner',
        'currency',
        'accompagning_amount'
    ];
    protected $translatable = ['theme', 'title'];

    public function hostCountry()
    {
        return $this->belongsTo(Country::class, 'host_country_id');
    }

    public function invitationLetter()
    {
        //return $this->belongsTo(InvitationLetter::class, 'invitation_letter_id');
    }

    public function periodes()
    {
        return $this->hasMany(Periode::class, 'congres_id');
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'congres_id');
    }

}
