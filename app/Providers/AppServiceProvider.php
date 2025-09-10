<?php

declare(strict_types=1);

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Model::shouldBeStrict($this->app->isLocal());
        DB::prohibitDestructiveCommands($this->app->isProduction());

        RateLimiter::for('ehealth-employee-get', function (object $job) {
            return Limit::perMinute(config('ehealth.rate_limit.employee_request'))->by($job->user->id);
        });

        RateLimiter::for('ehealth-division-get', function (object $job) {
            echo "Rate limiter set for user: " . $job->user->id . PHP_EOL;
            return Limit::perMinute(config('ehealth.rate_limit.division_request'))->by($job->user->id);
        });

        // RateLimiter::for('ehealth-division-get', fn (object $job) => Limit::perMinute(50)->by($job->user->id));
    }
}
