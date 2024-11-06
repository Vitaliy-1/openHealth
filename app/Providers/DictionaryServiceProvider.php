<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use App\Services\DictionaryService;

class DictionaryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register DictionaryService.
     */
    public function register(): void
    {
        $this->app->singleton(DictionaryService::class, fn ($app) => new DictionaryService($app['config']['dictionary']));
    }

    /**
     * Get the DictionaryService provided by the provider
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [DictionaryService::class];
    }
}
