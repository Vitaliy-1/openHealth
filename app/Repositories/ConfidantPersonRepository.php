<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Person\Person;
use App\Models\Relations\ConfidantPerson;

class ConfidantPersonRepository
{
    public function addConfidantPerson(object $model, array $confidantPersons): void
    {
        if (!empty($confidantPersons)) {
            // Get person_id by uuid
            if (isset($confidantPersons['person_id'])) {
                $person = Person::where('uuid', $confidantPersons['person_id'])->first();
            }

            // Formatting data array
            $confidantPersonData = $confidantPersons['confidantPersonInfo'];
            $confidantPersonData['person_request_id'] = $model->id;
            $confidantPersonData['person_id'] = $person->id ?? null;
            $confidantPersonData['person_uuid'] = $confidantPersonData['id'] ?? $confidantPersonData['person_uuid'];
            unset($confidantPersonData['id']);

            $confidantPersonData['documents_relationship'] = $confidantPersons['documents_relationship'];

            $confidantPerson = ConfidantPerson::updateOrCreate([
                'person_request_id' => $model->id
            ],
                $confidantPersonData
            );

            if (!empty($confidantPersonData['phones'])) {
                $this->savePhonesRelationship($confidantPerson, $confidantPersonData['phones']);
            }

            $model->confidantPerson()->save($confidantPerson);
        }
    }

    /**
     * Create phones relations with confidant person.
     *
     * @param  ConfidantPerson  $confidantPerson
     * @param  array  $phones
     * @return void
     */
    private function savePhonesRelationship(ConfidantPerson $confidantPerson, array $phones): void
    {
        foreach ($phones as $phone) {
            $confidantPerson->phones()->create($phone);
        }
    }
}
