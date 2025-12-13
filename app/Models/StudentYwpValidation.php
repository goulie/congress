<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudentYwpValidation extends Model
{
    //enum status
    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    protected $table = 'student_ywp_validations';
    protected $fillable = ['status', 'reason', 'validator_id','participant_id'];

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    //Approved Students
    public static function getAcceptedStudents()
    {
        return self::where('status', self::STATUS_APPROVED)->whereIn('participant_id', Participant::getLastCongressStudents()->pluck('id'))->get();
    }

    //Rejected Students
    public static function getRejectedStudents()
    {
        return self::where('status', self::STATUS_REJECTED)->whereIn('participant_id', Participant::getLastCongressStudents()->pluck('id'))->get();
    }

    //Pending Students
    public static function getPendingStudents()
    {
        return self::where('status', self::STATUS_PENDING)->whereIn('participant_id', Participant::getLastCongressStudents()->pluck('id'))->get();
    }

    //Approved YWP
    public static function getAcceptedYwp()
    {
        return self::where('status', self::STATUS_APPROVED)->whereIn('participant_id', Participant::getLastCongressYWP()->pluck('id'))->get();
    }

    //Rejected YWP
    public static function getRejectedYwp()
    {
        return self::where('status', self::STATUS_REJECTED)->whereIn('participant_id', Participant::getLastCongressYWP()->pluck('id'))->get();
    }

    //Pending YWP
    public static function getPendingYwp()
    {
        return self::where('status', self::STATUS_PENDING)->whereIn('participant_id', Participant::getLastCongressYWP()->pluck('id'))->get();
    }

}
