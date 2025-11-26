<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MembershipTypeMember extends Model
{
    protected $table = 'membership_type_members';
    protected $fillable = ['libelle','indice_debut'];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
