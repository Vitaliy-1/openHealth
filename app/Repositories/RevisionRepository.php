<?php

namespace App\Repositories;

use App\Models\Revision;

class RevisionRepository
{
    /**
     * @param object $model
     * @param array $educations
     *
     * @return void
     */
    public function saveRevision(object $model, array $revisionData): void
    {
        if (empty($revisionData)) {
            return;
        }

        $revisionParams = [
            'revisionable_type' => get_class($model),
            'revisionable_id'   => $model->id
        ];

        $revision = Revision::where($revisionParams)->first() ?? $this->createRevision($model);

        $revision->fill($revisionData);

        $model->revision()->save($revision);
    }

    // Create the new model depends on incoming model type
    protected function createRevision(object $model): Revision
    {
        $revision = new Revision();
        $revision->revisionable_type = get_class($model);
        $revision->revisionable_id = $model->id;

        return $revision;
    }
}
