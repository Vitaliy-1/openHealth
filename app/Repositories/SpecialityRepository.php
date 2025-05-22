<?php

namespace App\Repositories;

use App\Models\Relations\Speciality;

class SpecialityRepository
{
    /**
     * @param object $model
     * @param array $specialities
     *
     * @return void
     */
    public function addSpecialities(object $model, array $specialities): void
    {
        if (empty($specialities)) {
            return;
        }

        foreach ($specialities as $specialityData) {
            $speciality = Speciality::where(
                [
                    'specialityable_type' => get_class($model),
                    'specialityable_id'   => $model->id
                ]
            )->first();

            if (!$speciality) {
                $speciality = new Speciality();
                $speciality->specialityable_type = get_class($model);
                $speciality->specialityable_id = $model->id;
            }

            $speciality->fill($specialityData);

            $model->specialities()->save($speciality);
        }
    }
}
