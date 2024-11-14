<?php

namespace App\Repositories;

use App\Models\Relations\Qualification;

class QualificationRepository
{


    /**
     * @param object $model
     * @param array $qualifications
     * @return void
     */
    public function addQualifications(object $model, array $qualifications): void
    {
        if (!empty($qualifications)) {
            foreach ($qualifications as $qualificationData) {
                $qualification = Qualification::firstOrNew(
                    [
                        'qualificationable_type' => get_class($model),
                        'qualificationable_id'   => $model->id
                    ],
                    $qualificationData
                );
                $model->qualifications()->save($qualification);
            }
        }

    }

}
