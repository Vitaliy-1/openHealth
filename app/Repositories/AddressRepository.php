<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Relations\Address;

class AddressRepository
{
    /**
     * Save address to DB using morphTo relation
     *
     * @param  object  $model
     * @param  array  $addresses
     * @return void
     */
    public function addAddresses(object $model, array $addresses): void
    {
        if (!empty($addresses)) {
            foreach ($addresses as $addressData) {
                $address = Address::firstOrNew(
                    [
                        'addressable_type' => get_class($model),
                        'addressable_id' => $model->id
                    ]
                );

                $address->fill($addressData);

                $model->address()->save($address);
            }
        }
    }
}
