<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\MedicalEvents\CodeableConceptRepository;
use App\Repositories\MedicalEvents\CodingRepository;
use App\Repositories\MedicalEvents\ConditionRepository;
use App\Repositories\MedicalEvents\EncounterRepository;
use App\Repositories\MedicalEvents\EpisodeRepository;
use App\Repositories\MedicalEvents\IdentifierRepository;
use App\Repositories\MedicalEvents\ImmunizationRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class MedicalEventsDBServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindRepository(EncounterRepository::class);
        $this->bindRepository(IdentifierRepository::class);
        $this->bindRepository(CodingRepository::class);
        $this->bindRepository(CodeableConceptRepository::class);
        $this->bindRepository(EpisodeRepository::class);
        $this->bindRepository(ConditionRepository::class);
        $this->bindRepository(ImmunizationRepository::class);
    }

    /**
     * Bind repository class to the service container with appropriate model based on driver.
     *
     * @param  string  $repositoryClass  The fully qualified repository class name
     * @return void
     */
    protected function bindRepository(string $repositoryClass): void
    {
        $this->app->bind($repositoryClass, function () use ($repositoryClass) {
            $driver = config('database.medical_events_db_driver');

            // Get the model name from the repository class name
            $modelName = str_replace('Repository', '', class_basename($repositoryClass));

            $modelClass = match ($driver) {
                'sql' => "App\\Models\\MedicalEvents\\Sql\\$modelName",
                'mongo' => "App\\Models\\MedicalEvents\\Mongo\\$modelName",
                default => throw new RuntimeException("Unsupported driver [$driver]")
            };

            return new $repositoryClass(new $modelClass());
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            EncounterRepository::class,
            IdentifierRepository::class,
            CodingRepository::class,
            CodeableConceptRepository::class,
            EpisodeRepository::class,
            ConditionRepository::class,
            ImmunizationRepository::class,
        ];
    }
}
