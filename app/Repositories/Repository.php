<?php

declare(strict_types=1);

namespace App\Repositories;

final class Repository
{
    public static function address(): AddressRepository
    {
        return app(AddressRepository::class);
    }

    public static function phone(): PhoneRepository
    {
        return app(PhoneRepository::class);
    }

    public static function document(): DocumentRepository
    {
        return app(DocumentRepository::class);
    }

    public static function authenticationMethod(): AuthenticationMethodRepository
    {
        return app(AuthenticationMethodRepository::class);
    }

    public static function confidantPerson(): ConfidantPersonRepository
    {
        return app(ConfidantPersonRepository::class);
    }
}
