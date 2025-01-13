<?php

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Builder showPersonRequest(string $id)
 */
class PersonRequest extends BasePerson
{
    public function __construct()
    {
        parent::__construct();
        $this->mergeFillable(['status', 'person_id', 'authorize_with']);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function scopeShowPersonRequest(Builder $query, string $id): array
    {
        /** @var PersonRequest $patientData */
        $patientData = $query->findOrFail($id);

        $patient = $patientData->toArray() ?? [];

        $patient['phones'] = $patientData->phones()->get()->toArray() ?? [];
        $patient['authentication_methods'] = $patientData->authenticationMethod()->get()->toArray() ?? [];

        $patientData->documents = $patientData->documents()->get()->toArray() ?? [];
        $patientData->addresses = $patientData->address()->get()->toArray() ?? [];
        $patientData->documentsRelationship = $patientData->confidantPerson()->get()->toArray() ?? [];

        $result = [
            'patient' => $patient,
            'documents' => $patientData->documents,
            'addresses' => $patientData->addresses[0],
            'documentsRelationship' => $patientData->documentsRelationship
        ];

        return arrayKeysToCamel($result);
    }
}
