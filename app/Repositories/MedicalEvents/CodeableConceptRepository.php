<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Models\MedicalEvents\Sql\CodeableConcept as SqlCodeableConcept;
use App\Models\MedicalEvents\Sql\Identifier as SqlIdentifier;

class CodeableConceptRepository extends BaseRepository
{
    /**
     * Create codeable concept in DB by provided data and attach coding.
     *
     * @param  array  $codeableConceptData
     * @return SqlCodeableConcept
     */
    public function store(array $codeableConceptData): SqlCodeableConcept
    {
        $codeableConcept = $this->model::create([
            'text' => $codeableConceptData['text'] ?? null
        ]);

        $codeableConcept->coding()->create([
            'system' => $codeableConceptData['coding'][0]['system'],
            'code' => $codeableConceptData['coding'][0]['code']
        ]);

        return $codeableConcept;
    }

    /**
     * Create codeable concept in DB for identifier.
     *
     * @param  SqlIdentifier  $identifier
     * @param  array  $codeableConceptData
     * @return SqlCodeableConcept
     */
    public function attach(SqlIdentifier $identifier, array $codeableConceptData): SqlCodeableConcept
    {
        /** @var SqlCodeableConcept $codeableConcept */
        $codeableConcept = $identifier->type()->create([
            'text' => $codeableConceptData['identifier']['type']['text'] ?? ''
        ]);

        $codeableConcept->coding()->create([
            'system' => $codeableConceptData['identifier']['type']['coding'][0]['system'],
            'code' => $codeableConceptData['identifier']['type']['coding'][0]['code']
        ]);

        return $codeableConcept;
    }
}
