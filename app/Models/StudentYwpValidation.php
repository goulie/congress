<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class StudentYwpValidation extends Model
{
    //enum status
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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
}
