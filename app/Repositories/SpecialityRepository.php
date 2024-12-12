<?php

namespace App\Repositories;

use App\Models\Relations\Speciality;

class SpecialityRepository
{


    /**
     * @param object $model
     * @param array $specialities
     */
    public function addSpecialities(object $model, array $specialities): void
    {
        if (!empty($specialities)) {
            foreach ($specialities as $specialityData) {
                $speciality = Speciality::firstOrNew(
                    [
                        'specialityable_type' => get_class($model),
                        'specialityable_id'   => $model->id
                    ],
                    $specialityData
                );
                $model->specialities()->save($speciality);
            }
        }

    }

}
