<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ScanneHistory extends Model
{
    protected $table = 'scanne_histories';
   
    protected $fillable = [
        'congres_id', 'participant_id', 'session_id', 'scanne_date'
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }
}
