<?php

namespace App\Repositories;

use App\Models\Relations\Phone;

class PhoneRepository
{
    /**
     * @param  object  $model
     * @param  array  $phones
     *
     * @return void
     */
    public function addPhones(object $model, array $phones): void
    {
        if (empty($phones)) {
            return;
        }

        foreach ($phones as $phoneData) {
            $phone = Phone::updateOrCreate([
                'phoneable_type' => get_class($model),
                'phoneable_id' => $model->id,
                'number' => $phoneData['number']
            ],
                $phoneData
            );

            $model->phones()->save($phone);
        }
    }

    /**
     * Sync Phones data to currant ($phones) state.
     * If $phones is empty the existent data just  will delete.
     *
     * @param  object  $model
     * @param  array  $phones
     *
     * @return void
     */
    public function syncPhones(object $model, array $phones): void
    {
        //Remove all phones records belongs to the $model
        Phone::where([
                    'phoneable_type' => get_class($model),
                    'phoneable_id' => $model->id
                ])
                ->delete();

        if (empty($phones)) {
            return;
        }

        foreach ($phones as $phoneData) {
            $phone = Phone::updateOrCreate([
                'phoneable_type' => get_class($model),
                'phoneable_id' => $model->id,
                'number' => $phoneData['number']
            ],
                $phoneData
            );

            $model->phones()->save($phone);
        }
    }
}
