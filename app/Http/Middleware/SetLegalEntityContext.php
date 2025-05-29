<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LegalEntityContext;
use App\Models\LegalEntity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
             if ($legalEntity) { Log::info('LegalEntity found by UUID', ['uuid' => $identifiedUuid, 'id' => $legalEntity->id]); }
             else { Log::warning('LegalEntity NOT found by UUID', ['uuid' => $identifiedUuid]); }
        }

        if (is_null($legalEntity) && Auth::guard('ehealth')->check()) {
            $user = Auth::guard('ehealth')->user();

            if ($user->legalEntity) {
                $legalEntity = $user->legalEntity;
                 Log::info('LegalEntity found through authenticated user (ehealth guard)', ['user_id' => $user->id, 'legal_entity_id' => $legalEntity->id]);
            } else {
                 Log::warning('Authenticated user (ehealth guard) has NO associated LegalEntity.', ['user_id' => $user->id]);
            }
        } elseif (is_null($legalEntity) && !Auth::guard('ehealth')->check()) {
             Log::info('No LegalEntity found and user not authenticated via ehealth guard.');
        }

        $this->legalEntityContext->set($legalEntity);

        return $next($request);
    }
}
