<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->addNonemptyDirective();
    }

    protected function addNonemptyDirective(): void
    {
        Blade::directive('nonempty', fn($expr) => "<?php if(!empty($expr)): ?>");

        Blade::directive('elsenonempty', fn($expr) => "<?php else: ?>");

        Blade::directive('endnonempty', fn() => "<?php endif; ?>");
    }
}
