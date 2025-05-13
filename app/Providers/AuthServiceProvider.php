<?php

namespace App\Providers;

use App\Auth\EHealth\Guards\EHealthGuard;
use App\Auth\EHealth\Providers\EHealthUserProvider;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use App\Policies\PatientPolicy;
use App\Auth\EHealth\Services\TokenStorage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Cookie\QueueingFactory;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PersonRequest::class => PatientPolicy::class,
        Person::class => PatientPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Auth::extend('ehealth', function($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            $tokenStorage = $app->make(TokenStorage::class);

            $guard = new EHealthGuard($name, $provider, $app['session.store'], $app['request'], $tokenStorage);

            $guard->setCookieJar($app->make(QueueingFactory::class));

            return $guard;
        });

        Auth::provider('ehealth_user_provider', function($app, array $config) {
            return new EHealthUserProvider($app['hash'], $config['model']);
        });
    }
}
