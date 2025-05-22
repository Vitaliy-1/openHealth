<?php

namespace App\Repositories;

use App\Models\Relations\Education;

class EducationRepository
{
    /**
     * @param object $model
     * @param array $educations
     *
     * @return void
     */
    public function addEducations(object $model, array $educations): void
    {
        if (empty($educations)) {
            return;
        }

        foreach ($educations as $educationData) {
            $education = Education::where(
                [
                    'educationable_type' => get_class($model),
                    'educationable_id'   => $model->id
                ]
            )->first();

            if (!$education) {
                    $education = new Education();
                    $education->educationable_type = get_class($model);
                    $education->educationable_id = $model->id;
            }

            $education->fill($educationData);

            $model->educations()->save($education);
        }
    }
}
