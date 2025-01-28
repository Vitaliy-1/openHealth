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
                $authenticationMethod = AuthenticationMethod::updateOrCreate([
                    'authenticatable_type' => get_class($model),
                    'authenticatable_id' => $model->id
                ],
                    $authenticationMethodData
                );

                $model->authenticationMethod()->save($authenticationMethod);
            }
        }
    }
}
