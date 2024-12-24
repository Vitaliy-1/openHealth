<?php

namespace App\Repositories;

use App\Models\Relations\Phone;

class PhoneRepository
{
    /**
     * @param  object  $model
     * @param  array  $phones
     * @return void
     */
    public function addPhones(object $model, array $phones): void
    {
        if (!empty($phones)) {
            foreach ($phones as $phoneData) {
                $phone = Phone::firstOrNew(
                    [
                        'phoneable_type' => get_class($model),
                        'phoneable_id' => $model->id,
                        'number' => $phoneData['number'],
                        'type' => $phoneData['type']
                    ],
                    $phoneData
                );

                $model->phones()->save($phone);
            }
        }
    }
}
