<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use App\Policies\PatientPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Jetstream\Jetstream;

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
        // Disable the default Jetsream's logout route
        Jetstream::ignoreRoutes(['logout']);
    }
}
