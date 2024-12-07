<?php

namespace App\Classes\eHealth\Providers;

use App\Classes\eHealth\Services\SchemaService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class SchemaServiceProvider extends ServiceProvider implements DeferrableProvider {
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SchemaService::class, function ($app) {
            return new SchemaService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [SchemaService::class];
    }
}
