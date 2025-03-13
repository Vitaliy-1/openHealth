<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use App\Services\DictionaryService;

class DictionaryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register DictionaryService.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(DictionaryService::class, fn (Application $app) => new DictionaryService($app['config']['dictionary']));
    }

    /**
     * Get the DictionaryService provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [DictionaryService::class];
    }
}
