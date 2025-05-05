<?php

namespace App\Http\Middleware;

use App\Services\LegalEntityContext;
use Closure;
use Illuminate\Http\Request;

class SetLegalEntityContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->legalEntity) {
            app(LegalEntityContext::class)->set($user->legalEntity);
        }

        return $next($request);
    }
}
