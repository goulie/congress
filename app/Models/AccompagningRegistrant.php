<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AccompagningRegistrant extends Model
{
    protected $table = 'accompagning_registrants';
    protected $fillable = [
        'civility_id',
        'name',
        'firstname',
        'email',
        'phone',
        'type_accompagning_id',
        'gender_id',
        'passeport_number',
        'passeport_photo',
        'user_id',
        'congres_id',
    ];

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function civility()
    {
        return $this->belongsTo(Civility::class, 'civility_id');
    }

    public function type_accompagning()
    {
        return $this->belongsTo(AccompanyingPersonType::class, 'type_accompagning_id');
    }
}
