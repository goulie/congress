<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use TCG\Voyager\Models\Category;

class Participant extends Model
{
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
        'langue',
    ];

    protected static function booted()
    {
        static::creating(function ($participant) {
            // ne pas écraser si déjà défini
            if (empty($participant->uuid)) {
                $participant->uuid = (string) Str::uuid();
                $participant->langue = app()->getLocale();
            }
        });
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
        return $this->belongsTo(CategorieRegistrant::class, 'type_member_id');
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
}
