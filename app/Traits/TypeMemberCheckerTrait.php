<?php

namespace App\Traits;

use App\Models\MembershipTypeMember;

trait TypeMemberCheckerTrait
{
    /**
     * Vérifie les trois premières lettres du code membre
     * et retourne l'ID correspondant dans membership_type_members.
     *
     * @param string $code
     * @return int|null
     */
    public function getMembershipTypeIdFromCode($code)
    {
        if (strlen($code) < 3) {
            return null; 
        }

        $prefix = strtoupper(substr($code, 0, 4));

        $type = MembershipTypeMember::where('indice_debut', $prefix)->first();

        return $type ? $type->id : null;
    }
}
