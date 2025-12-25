<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Session extends Model
{
    use Translatable;
    protected $table = 'sessions';
    protected $fillable = [
        'congres_id',
        'libelle',
        'date_session',
        'status',
        'type_session'
    ];
    protected $translatable = ['libelle'];

    protected static function booted()
    {
        static::creating(function ($session) {

            if (empty($session->congres_id)) {
                $session->congres_id = Congress::latest()->first()->id;
            }
        });
    }

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }

    public function ScannHistories()
    {
        return $this->Hasmany(ScanneHistory::class);
    }
    //Current Session
    public static function CurrentSession()
    {
        return self::where([
            'status' => 'Active',
            'congres_id' => Congress::latest()->first()->id,
            'date_session' => Carbon::now()->format('Y-m-d')
        ])->get();
    }
}
