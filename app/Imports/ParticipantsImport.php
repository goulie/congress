<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\Civility;
use App\Models\Gender;
use App\Models\Country;
use App\Models\StudentLevel;
use App\Models\TypeOrganisation;
use App\Models\CategoryParticipant;
use App\Models\TypeMember;
use App\Models\Congress;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Vérifie que la ligne contient bien un email avant de continuer
            if (empty($row['email'])) continue;

            // Correspondances texte → ID
            $civility_id = $this->getId(Civility::class, $row['title']);
            $gender_id = $this->getId(Gender::class, $row['gender']);
            $country_id = $this->getIdCountry(Country::class, $row['country']);
            $student_level_id = $this->getId(StudentLevel::class, $row['education_level']);
            $org_type_id = $this->getId(TypeOrganisation::class, $row['organisation_type']);
            $category_id = $this->getId(CategoryParticipant::class, $row['category']);
            $type_member_id = $this->getId(TypeMember::class, $row['membership']);

            $congres = Congress::latest()->first();

            Participant::updateOrCreate(
                ['email' => $row['email']],
                ['civility_id' => $civility_id,
                'fname' => $row['first_name'],
                'lname' => $row['last_name'],
                'student_level_id' => $student_level_id,
                'gender_id' => $gender_id,
                'nationality_id' => $country_id,
                'email' => $row['email'],
                'phone' => $row['phone'],
                'organisation' => $row['organisation'],
                'organisation_type_id' => $org_type_id,
                'job' => $row['position'],
                'participant_category_id' => $category_id,
                'type_member_id' => $type_member_id,
                'membership_code' => $row['membership_id'],
                'passeport_number' => $row['nin_passport_number'] ?? null,
                'diner' => strtolower(trim($row['gala_dinner'] ?? 'no')) == 'yes' ? 'oui' : 'non',
                'visite' => strtolower(trim($row['technical_tours'] ?? 'no')) == 'yes' ? 'oui' : 'non',
                'congres_id' => $congres?->id,
                'type_participant' => 'group',
                'currency' => $congres?->currency ?? 'USD',
                ]
            );
        }
    }

    private function getId($model, $name)
    {
        if (!$name) return null;
        $instance = $model::firstOrCreate(['libelle' => trim($name)]);
        return $instance->id;
    }
    private function getIdCountry($model, $name)
    {
        if (!$name) return null;
        $instance = $model::firstOrCreate(['libelle_en' => trim($name)]);
        return $instance->id ?? null;
    }
}
