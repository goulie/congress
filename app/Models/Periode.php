<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

class Periode extends Model
{
    use Translatable;

    protected $table = 'periodes';
    protected $fillable = ['libelle', 'congres_id', 'start_date', 'end_date'];
    protected $translatable = ['libelle'];

    public function congres()
    {
        return $this->belongsTo(Congress::class);
    }

    /**
     * Trouver la période active pour un congrès à une date donnée
     */
    public static function PeriodeActive($congresId, $dateReference = null)
    {
        if ($dateReference === null) {
            $dateReference = Carbon::now();
        }

        return self::where('congres_id', $congresId)
            ->where('start_date', '<=', $dateReference)
            ->where('end_date', '>=', $dateReference)
            ->first();
    }

    public function joursRestants()
    {
        return Carbon::now()->diffInDays(Carbon::parse($this->end_date), false);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class);
    }
}
