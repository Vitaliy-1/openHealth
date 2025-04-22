<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LegalEntityContext;
use App\Models\LegalEntity;
use Illuminate\Support\Facades\Auth;

class SetLegalEntityContext
{
    protected LegalEntityContext $legalEntityContext;

    public function __construct(LegalEntityContext $legalEntityContext)
    {
        $this->legalEntityContext = $legalEntityContext;
    }

    public function handle(Request $request, Closure $next)
    {
        $legalEntity = null;

        $legalEntityUuidFromRoute = $request->route('legal_entity_uuid');
        $legalEntityUuidFromQuery = $request->query('le_uuid');
        $identifiedUuid = $legalEntityUuidFromRoute ?? $legalEntityUuidFromQuery;

        if ($identifiedUuid) {
            $legalEntity = LegalEntity::where('uuid', $identifiedUuid)->first();
        }

        if (is_null($legalEntity) && Auth::guard('ehealth')->check()) {
            $user = Auth::guard('ehealth')->user();

            if ($user->legalEntity) {
                $legalEntity = $user->legalEntity;
            }
        }

        $this->legalEntityContext->set($legalEntity);

        return $next($request);
    }
}
