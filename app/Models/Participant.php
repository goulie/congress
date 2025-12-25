<?php

namespace App\Models;

use App\Traits\GenerateCodeQrTrait;
use Doctrine\DBAL\Types\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Category;

class Participant extends Model
{
    use GenerateCodeQrTrait;
    use Notifiable;
    protected $table = 'participants';
    protected $fillable = [
        'civility_id',
        'fname',
        'lname',
        'student_level_id',
        'gender_id',
        'nationality_id',
        'email',
        'phone',
        'organisation',
        'organisation_type_id',
        'job',
        'participant_category_id',
        'type_member_id',
        'membership_code',
        'diner',
        'visite',
        'passeport_number',
        'passeport_pdf',
        'invitation_letter',
        'author',
        'user_id',
        'registration_id',
        'organisation_type_other',
        'student_level_other',
        'congres_id',
        'type_participant',
        'type_accompagning_id',
        'invoice_number',
        'amount',
        'currency',
        'status',
        'uuid',
        'student_card',
        'student_letter',
        'deleguate_day',
        'langue',
        'site_visit_id',
        'membre_aae',
        'pass_deleguate',
        'age_range_id',
        'job_country_id',
        'isYwpOrStudent',
        'badge_full_name',
        'badge_color_id',
        'ywp_or_student',
        'expiration_passeport_date',
        'code_path',
        'sigle_organisation',
        'role_badge_congres'
    ];

    protected static function booted()
    {
        static::creating(function ($participant) {

            if (empty($participant->uuid)) {
                $code = uniqid(20);
                $participant->uuid = $code;
                $participant->langue = app()->getLocale();
                $participant->code_path = $participant->generateAndStoreQrCode($code);
            }
        });
    }

    public function ScannHistories()
    {
        return $this->Hasmany(ScanneHistory::class);
    }

    public function ageRange()
    {
        return $this->belongsTo(AgeRange::class, 'age_range_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function civility()
    {
        return $this->belongsTo(Civility::class, 'civility_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function studentLevel()
    {
        return $this->belongsTo(StudentLevel::class, 'student_level_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Country::class, 'nationality_id');
    }

    public function participantCategory()
    {
        return $this->belongsTo(CategoryParticipant::class, 'participant_category_id');
    }

    public function typeMember()
    {
        return $this->belongsTo(MembershipTypeMember::class, 'type_member_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function type_accompagning()
    {
        return $this->belongsTo(AccompanyingPersonType::class, 'type_accompagning_id');
    }

    public function congres()
    {
        return $this->belongsTo(Congress::class, 'congres_id');
    }


    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'participant_id');
    }

    public function siteVisite()
    {
        return $this->belongsTo(SiteVisite::class, 'site_visit_id');
    }

    public function jobCountry()
    {
        return $this->belongsTo(Country::class, 'job_country_id');
    }


    public function badge_color()
    {
        return $this->belongsTo(BadgeColor::class, 'badge_color_id');
    }

    public function organisationType()
    {
        return $this->belongsTo(TypeOrganisation::class, 'organisation_type_id');
    }

    public function validation_ywp_students()
    {
        return $this->hasMany(StudentYwpValidation::class);
    }

    // Ajoutez cette méthode pour vérifier facilement s'il y a une validation en attente
    public function hasPendingYwpValidation()
    {
        if (!$this->validation_ywp_students || $this->validation_ywp_students->isEmpty()) {
            return false;
        }

        $latest = $this->validation_ywp_students->last();
        return $latest && $latest->status == StudentYwpValidation::STATUS_PENDING;
    }

    // Optionnel : méthode pour obtenir la dernière validation
    public function getLatestYwpValidationAttribute()
    {
        return $this->validation_ywp_students->last();
    }

    private static function getLastCongress()
    {
        return Congress::orderBy('id', 'desc')->first();
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
    /**
     * Liste des participants du dernier congrès
     */
    public static function getLastCongressParticipants()
    {
        $lastCongress = self::getLastCongress();

        if (!$lastCongress) {
            return collect();
        }


        return self::where('participants.congres_id', $lastCongress->id)
            ->whereNotNull('participants.email')
            ->with([
                'civility',
                'country',
                'gender',
                'studentLevel',
                'participantCategory',
                'typeMember',
                'organisationType',
                'badge_color',
                'invoices'
            ])->join('invoices', 'participants.id', '=', 'invoices.participant_id')
            ->orderBy('participants.created_at', 'desc')
            ->get();
    }

    /**
     * Liste des étudiants du dernier congrès
     */
    public static function getLastCongressStudents()
    {
        $lastCongress = self::getLastCongress();

        if (!$lastCongress) {
            return collect();
        }

        return self::whereNotNull('participants.email')
            ->where(function ($query) {
                $query->where('ywp_or_student', 'student');
            })
            ->with([
                'civility',
                'country',
                'gender',
                'studentLevel',
                'participantCategory',
                'typeMember',
                'organisationType',
                'badge_color'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Liste des YWP (Young Water Professionals) du dernier congrès
     */
    public static function getLastCongressYWP()
    {
        $lastCongress = self::getLastCongress();

        if (!$lastCongress) {
            return collect();
        }

        return self::whereNotNull('email')
            ->where(function ($query) {
                $query->where('ywp_or_student', 'ywp');
            })
            ->with([
                'civility',
                'country',
                'gender',
                'studentLevel',
                'participantCategory',
                'typeMember',
                'organisationType',
                'badge_color'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Statistiques pour le tableau de bord
     */
    public static function getDashboardStats()
    {
        $lastCongress = self::getLastCongress();

        if (!$lastCongress) {
            return [
                'total' => 0,
                'students' => 0,
                'ywp' => 0,
                'validated' => 0,
                'pending' => 0
            ];
        }


        $students = self::getLastCongressParticipants()
            ->where(function ($query) {
                $query->where('ywp_or_student', 'student')
                    ->orWhere('isYwpOrStudent', 'student')
                    ->orWhereNotNull('student_level_id');
            })->count();

        $ywp = self::getLastCongressParticipants()
            ->where(function ($query) {
                $query->where('ywp_or_student', 'ywp')
                    ->orWhere('isYwpOrStudent', 'ywp')
                    ->orWhere('participant_category_id', function ($subquery) {
                        $subquery->select('id')
                            ->from('category_participants')
                            ->where('name', 'like', '%YWP%')
                            ->orWhere('name', 'like', '%Young Water Professional%');
                    });
            })->count();



        return [
            'total' => self::getLastCongressParticipants()->count(),
            'students' => $students,
            'ywp' => $ywp,
        ];
    }

    public static function participantsByCountry()
    {
        return self::select(
            'countries.id as country_id',
            'countries.libelle_pays',
            DB::raw('COUNT(participants.id) as total')
        )
            ->join('countries', 'countries.id', '=', 'participants.nationality_id')
            ->whereNotNull('participants.email')
            ->groupBy('countries.id', 'countries.libelle_fr')
            ->orderByDesc('total')
            ->get();
    }

    public static function countNationalities()
    {
        return self::whereNotNull('email')
            ->distinct('nationality_id')
            ->count('nationality_id');
    }
}
