<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Relations\Document;

class DocumentRepository
{
    /**
     * Add or update documents associated with a given model.
     *
     * @param  object  $model  The parent model to associate the documents with.
     * @param  array  $documents  An array of document data to be added or updated.
     * @return void
     */
    public function addDocuments(object $model, array $documents): void
    {
        if (!empty($documents)) {
            foreach ($documents as $documentData) {
                $document = Document::updateOrCreate([
                    'documentable_type' => get_class($model),
                    'documentable_id' => $model->id,
                    'type' => $documentData['type']
                ],
                    $documentData
                );

                $model->documents()->save($document);
            }
        }
    }
}
