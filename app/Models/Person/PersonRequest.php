<?php

declare(strict_types=1);

namespace App\Models\Person;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPersonRequest
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

    public function scopeShowPersonRequest(Builder $query, int $id): array
    {
        /** @var PersonRequest $patientData */
        $patientData = $query->findOrFail($id);

        $patient = $patientData->toArray() ?? [];

        $patient['phones'] = $patientData->phones()->get()->toArray() ?? [];
        $patient['authentication_methods'] = $patientData->authenticationMethod()->get()->toArray() ?? [];

        $patientData->documents = $patientData->documents()->get()->toArray() ?? [];
        $patientData->address = $patientData->address()->get()->toArray() ?? [];
        $patientData->confidantPerson = $patientData->confidantPerson()->get()->toArray() ?? [];

        $result = [
            'patient' => $patient,
            'documents' => $patientData->documents,
            'address' => $patientData->address[0],
            'confidantPerson' => $patientData->confidantPerson
        ];

        return arrayKeysToCamel($result);
    }
}
