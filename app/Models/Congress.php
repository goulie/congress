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
        'currency',
        'nbre_place_dinner',
        'banniere',
        'event_place',
        'banniere_badge',
    ];
    protected $translatable = ['theme', 'title'];

    public function hostCountry()
    {
        return $this->belongsTo(Country::class, 'host_country_id');
    }

    public function invitationLetter()
    {
        return $this->belongsTo(InvitationLetter::class, 'invitation_letter_id');
    }

    public function periodes()
    {
        return $this->hasMany(Periode::class, 'congres_id');
    }

    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'congres_id');
    }

    public function LatestCongress()
    {
        return self::latest()->first();
    }

    public function invitationLetters()
    {
        return $this->belongsTo(InvitationLetter::class, 'invitation_letter_id');
    }

    //COUNT DINNER PLACE REST
    public static function dinnerRest()
    {
        // Récupérer le dernier congrès
        $latestCongress = self::latest('id')->first();
        
        if (!$latestCongress) {
            return 0; // Ou une valeur par défaut appropriée
        }
        
        // Compter les participants avec diner = 'oui' et email non nul pour ce congrès
        $participantsWithDinner = Participant::where([
            'congres_id' => $latestCongress->id,
            'diner' => 'oui'
        ])->whereNotNull('email')->count();
        
        // Calculer les places restantes
        $remainingPlaces = $latestCongress->nbre_place_dinner - $participantsWithDinner;
        
        // S'assurer que la valeur n'est pas négative
        return max(0, $remainingPlaces);
    }

}
