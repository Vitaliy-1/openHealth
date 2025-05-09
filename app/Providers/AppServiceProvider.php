<?php

namespace App\Providers;

use App\Models\LegalEntity;
use App\Services\LegalEntityContext;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->singletonIf(LegalEntity::class, function () {

            return Auth::user()?->legalEntity;
        });

        // додатково alias для зручного доступу (опційно)
        $this->app->alias(LegalEntity::class, 'legalEntity');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
    }
}
