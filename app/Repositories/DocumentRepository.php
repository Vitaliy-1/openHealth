<?php

namespace App\Repositories;

use App\Models\Relations\Document;

class DocumentRepository
{

    /**
     * @param object $model
     * @param array $documents
     * @return void
     */

    public function addDocuments( object $model, array $documents): void
    {
        if (!empty($documents)) {
            foreach ($documents as $documentData) {
                $document = Document::firstOrNew(
                    [
                        'documentable_type' => get_class($model),
                        'documentable_id'   => $model->id
                    ],
                    $documentData
                );
                $model->documents()->save($document);
            }
        }

    }

}
