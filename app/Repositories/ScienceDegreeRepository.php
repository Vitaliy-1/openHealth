<?php

namespace App\Repositories;

use App\Models\Relations\ScienceDegree;

class ScienceDegreeRepository
{


    /**
     * @param object $model
     * @param array $scienceDegrees
     * @return void
     */
    public function addScienceDegrees(object $model, array $scienceDegrees): void
    {
        if (!empty($scienceDegrees)) {
            foreach ($scienceDegrees as $scienceDegreeData) {
                $scienceDegree = ScienceDegree::firstOrNew(
                    [
                        'science_degreeable_type' => get_class($model),
                        'science_degreeable_id'   => $model->id
                    ],
                    $scienceDegreeData
                );
                $model->scienceDegrees()->save($scienceDegree);
            }
        }

    }

}
