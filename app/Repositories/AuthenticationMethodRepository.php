<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Relations\AuthenticationMethod;

class AuthenticationMethodRepository
{
    public function addAuthenticationMethod(object $model, array $authenticationMethods): void
    {
        if (!empty($authenticationMethods)) {
            foreach ($authenticationMethods as $authenticationMethodData) {
                $authenticationMethodData['person_request_id'] = $model->id;

                $authenticationMethod = AuthenticationMethod::firstOrNew(
                    [
                        'person_request_id' => $model->id,
                        'type' => $authenticationMethodData['type']
                    ],
                    $authenticationMethodData
                );

                $model->authenticationMethod()->save($authenticationMethod);
            }
        }
    }
}
