<?php

namespace App\Repositories;

use App\Models\Relations\Education;

class EducationRepository
{


    /**
     * @param object $model
     * @param array $educations
     * @return void
     */
    public function addEducations(object $model, array $educations): void
    {
        if (!empty($educations)) {
            foreach ($educations as $educationData) {
                $education = Education::firstOrNew(
                    [
                        'educationable_type' => get_class($model),
                        'educationable_id'   => $model->id
                    ],
                    $educationData
                );
                $model->educations()->save($education);
            }
        }
    }

}
