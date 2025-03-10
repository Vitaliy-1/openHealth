<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Dictionary\v2\Dictionary;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class DictionaryV2ServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register DictionaryService.
     */
    public function register(): void
    {
        $this->app->singleton(Dictionary::class, static fn($app) => new Dictionary());
    }

    /**
     * Get the DictionaryService provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [Dictionary::class];
    }
}
