<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class InvitationLetter extends Model
{
    protected $table = 'invitation_letter';
    protected $fillable = [
        'header_logo',
        'subject',
        'content',
        'signature',
        'signatory_name',
        'signatory_job',
        'langue',
        'position_cachet'
    ];

    public function congres()
    {
        return $this->hasMany(Congress::class, 'invitation_letter_id');
    }
}
