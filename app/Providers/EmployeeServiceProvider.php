<?php

namespace App\Providers;

use App\Models\LegalEntity;
use App\Repositories\DocumentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PartyRepository;
use App\Repositories\PhoneRepository;
use App\Repositories\UserRepository;
use App\Services\EmployeeService;
use Illuminate\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the service  for the model Employee
        $this->app->singleton(EmployeeService::class, function ($app) {
            return new EmployeeService(
                $app->make(LegalEntity::class),
                $app->make(UserRepository::class),
                $app->make(EmployeeRepository::class),
                $app->make(PartyRepository::class),
                $app->make(PhoneRepository::class),
                $app->make(DocumentRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
