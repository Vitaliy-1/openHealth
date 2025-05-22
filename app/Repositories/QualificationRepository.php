<?php

namespace App\Repositories;

use App\Models\Relations\Qualification;

class QualificationRepository
{
    /**
     * @param object $model
     * @param array $qualifications
     *
     * @return void
     */
    public function addQualifications(object $model, array $qualifications): void
    {
        if (empty($qualifications)) {
            return;
        }

        foreach ($qualifications as $qualificationData) {
            $qualification = Qualification::where(
                [
                    'qualificationable_type' => get_class($model),
                    'qualificationable_id'   => $model->id
                ],
            )->first();

            if (!$qualification) {
                $qualification = new Qualification();
                $qualification->qualificationable_type = get_class($model);
                $qualification->qualificationable_id = $model->id;
            }

            $qualification->fill($qualificationData);

            $model->qualifications()->save($qualification);
        }
    }
}
