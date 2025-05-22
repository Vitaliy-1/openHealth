<?php

namespace App\Repositories;

use App\Models\Relations\ScienceDegree;

class ScienceDegreeRepository
{
    /**
     * @param object $model
     * @param array $scienceDegreeData
     * @return void
     */
    public function addScienceDegrees(object $model, array $scienceDegreeData): void
    {
        if (empty($scienceDegreeData)) {
            return;
        }

        $scienceDegree = ScienceDegree::where(
            [
                'science_degreeable_type' => get_class($model),
                'science_degreeable_id' => $model->id
            ],
        )->first();

        if (!$scienceDegree) {
            $scienceDegree = new ScienceDegree();
            $scienceDegree->science_degreeable_type = get_class($model);
            $scienceDegree->science_degreeable_id = $model->id;
        }

        $scienceDegree->fill($scienceDegreeData);

        $model->scienceDegrees()->save($scienceDegree);

    }
}
