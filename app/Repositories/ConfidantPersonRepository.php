<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Person;
use App\Models\Relations\ConfidantPerson;

class ConfidantPersonRepository
{
    public function addConfidantPerson(object $model, array $confidantPersons): void
    {
        if (!empty($confidantPersons)) {
            // Get person_id by uuid
            $person = Person::where('uuid', $confidantPersons['person_id'])->first();
            // Change uuid to id
            $confidantPersons['person_id'] = $person->id;

            $confidantPerson = ConfidantPerson::firstOrNew(
                [
                    'person_id' => $person->id,
                    'person_request_id' => $model->id
                ],
                $confidantPersons
            );

            $model->confidantPerson()->save($confidantPerson);
        }
    }
}
