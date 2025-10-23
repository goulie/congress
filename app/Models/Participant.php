<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Participant extends Model
{
    protected $table = 'participants';
    protected $fillable = ['civility_id', 'fname', 'lname', 'student_level_id', 'gender_id', 'nationality_id', 'email', 'phone', 'organisation', 'organisation_type_id', 'job', 'participant_category_id', 'type_member_id', 'membership_code', 'diner', 'visite', 'passeport_number', 'passeport_pdf', 'invitation_letter', 'author'];

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
        return $this->belongsTo(TypeMember::class, 'type_member_id');
    }
}
