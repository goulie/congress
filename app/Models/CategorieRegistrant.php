<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use TCG\Voyager\Traits\Translatable;

class CategorieRegistrant extends Model
{
    use Translatable;

    protected $table = 'categorie_registrants';
    protected $fillable = ['libelle', 'status'];
    protected $translatable = ['libelle'];

    public function tarifs()
    {
        return $this->hasMany(Tarif::class, 'categorie_registrant_id');
    }

    public static function getCategoryWithAmount($congresId, $status)
    {
        // Trouver la période active pour ce congrès
        $periodeActive = Periode::PeriodeActive($congresId, Carbon::now());

        if (!$periodeActive) {
            return null;
        }

        $categorie = self::select('id', 'libelle', 'status')
            ->where('status', $status)
            ->whereHas('tarifs', function ($q) use ($congresId, $periodeActive) {
                $q->where('congres_id', $congresId)
                    ->where('periode_id', $periodeActive->id);
            })
            ->with(['tarifs' => function ($q) use ($congresId, $periodeActive) {
                $q->select('id', 'categorie_registrant_id', 'montant', 'congres_id', 'periode_id')
                    ->where('congres_id', $congresId)
                    ->where('periode_id', $periodeActive->id);
            }])
            ->first();

        if (!$categorie) {
            return null;
        }

        return (object) [
            'libelle' => $categorie->libelle,
            'montant' => $categorie->tarifs->first()->montant ?? 0,
            'periode' => $periodeActive->libelle,
            'tarif_id' => $categorie->tarifs->first()->id ?? null,
            //'deadline' => $periodeActive->end_date,
        ];
    }


    public static function forCongress($congresId, $status = 'select')
    {
        // Trouver la période active du congrès
        $periodeActive = Periode::PeriodeActive($congresId, Carbon::now());

        if (!$periodeActive) {
            return collect(); // aucune période active
        }

        // Récupérer les catégories de ce type avec leur tarif de la période active
        $categories = self::where('status', $status)
            ->whereHas('tarifs', function ($q) use ($congresId, $periodeActive) {
                $q->where('congres_id', $congresId)
                    ->where('periode_id', $periodeActive->id);
            })
            ->with(['tarifs' => function ($q) use ($congresId, $periodeActive) {
                $q->select('id', 'categorie_registrant_id', 'montant', 'congres_id', 'periode_id')
                    ->where('congres_id', $congresId)
                    ->where('periode_id', $periodeActive->id);
            }])
            ->get();

        // On simplifie le résultat pour que chaque catégorie ait directement le montant et la période
        return $categories->map(function ($cat) use ($periodeActive) {
            return (object) [
                'id' => $cat->id,
                'libelle' => $cat->translate(app()->getLocale(), 'fallbackLocale')->libelle,
                'montant' => $cat->tarifs->first()->montant ?? 0,
                'periode' => $periodeActive->libelle,
            ];
        });
    }

    public static function student_ywp_memberForCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'student_ywp_member');
    }

    public static function deleguateForCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'deleguate');
    }
    public static function studentForCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'student_ywp');
    }

    public static function DinnerforCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'dinner');
    }

    public static function accompanyingPersonForCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'accompanying_person');
    }

    public static function ToursforCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'technical_tours');
    }

    public static function PassDeleguateforCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'deleguate_pass');
    }

    public static function NonMemberPriceforCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'non_member');
    }

    public static function StudentPriceforCongress($congresId)
    {
        return self::getCategoryWithAmount($congresId, 'student_ywp');
    }
    public function participants()
    {
        return $this->hasMany(Participant::class, 'participant_category_id');
    }


    public function getTarifForCurrentPeriod($congresId)
    {
        // Trouver la période active du congrès
        $periodeActive = Periode::PeriodeActive($congresId, Carbon::now());

        if (!$periodeActive) {
            return null; // aucune période active
        }

        // Récupérer le tarif correspondant à cette catégorie + période
        $tarif = $this->tarifs()
            ->where('congres_id', $congresId)
            ->where('periode_id', $periodeActive->id)
            ->first();

        if (!$tarif) {
            return null;
        }

        // Retourne un petit objet pratique
        return (object) [
            'montant' => $tarif->montant,
            'periode' => $periodeActive->libelle,
            'periode_id' => $periodeActive->id,
        ];
    }
}
