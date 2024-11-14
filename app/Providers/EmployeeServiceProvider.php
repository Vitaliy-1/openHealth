<?php

namespace App\Providers;

use App\Models\LegalEntity;
use App\Repositories\DocumentRepository;
use App\Repositories\EducationRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\PartyRepository;
use App\Repositories\PhoneRepository;
use App\Repositories\QualificationRepository;
use App\Repositories\ScienceDegreeRepository;
use App\Repositories\SpecialityRepository;
use App\Repositories\UserRepository;
use App\Services\EmployeeService;
use Illuminate\Support\ServiceProvider;

class EmployeeServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected  bool $defer = true;


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
                $app->make(DocumentRepository::class),
                $app->make(EducationRepository::class),
                $app->make(ScienceDegreeRepository::class),
                $app->make(QualificationRepository::class),
                $app->make(SpecialityRepository::class)
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
