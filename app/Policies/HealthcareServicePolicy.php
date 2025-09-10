<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Status;
use App\Models\LegalEntity;
use App\Models\HealthcareService;
use Illuminate\Auth\Access\Response;

// TODO: need to more test and review this policy
class HealthcareServicePolicy
{
    /**
     * User allowed to view the list of healthcare services
     */
    public function viewAny(User $user): Response
    {
        if ($user->cannot('healthcare_service:read')) {
            return Response::denyWithStatus(403);
        }

        return Response::allow();
    }

    /**
     * User allow to create the Division
     */
    public function create(User $user): Response
    {
        if ($user->cannot('healthcare_service:write')) {
            return Response::denyWithStatus(403);
        }

        return Response::allow();
    }

    /**
     * User allow to delete the Division's draft record fromm the DB
     */
    public function delete(User $user, HealthcareService $healthcareService): Response
    {
        // Only HealthcareServices with DRAFT status can be deleted
        if ($healthcareService->status !== Status::DRAFT) {
            return Response::deny();
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, HealthcareService $healthcareService, ?LegalEntity $legalEntity = null): Response|bool
    {
        if (is_null($legalEntity)) {
            $legalEntity = legalEntity();
        }

        // If got null instaed HealthcareService model
        if (empty($healthcareService)) {
            return Response::denyWithStatus(404);
        }

        // Should belong to the same legal entity
        if ($healthcareService->legal_entity_id !== (int) $legalEntity->id) {
            return Response::denyWithStatus(404);
        }

        if ($user->cannot('healthcare_service:write')) {
            return Response::denyWithStatus(403);
        }

        // Inactive healthcare services cannot be updated
        if ($healthcareService->status === Status::INACTIVE) {
            return Response::deny();
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can activate the division.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Division  $division
     *
     * @return bool
     */
    public function activate(User $user, HealthcareService $healthcareService): Response
    {
        if ($user->cannot('healthcare_service:write')) {
            return Response::denyWithStatus(403);
        }

        // Some healthcare services cannot be activated
        if (
            $healthcareService->status === Status::ACTIVE ||
            $healthcareService->status === Status::DRAFT ||
            $healthcareService->status === Status::UNSYNCED
        ) {
            return Response::deny();
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can deactivate the division.
     *
     * @param  \App\Models\User  $user  The user attempting the action
     * @param  \App\Models\Division  $division  The division to be deactivated
     *
     * @return bool  True if user can deactivate the division, false otherwise
     */
    public function deactivate(User $user, HealthcareService $healthcareService): Response
    {
        if ($user->cannot('healthcare_service:write')) {
            return Response::denyWithStatus(403);
        }

        // Some healthcare services cannot be deactivated
        if (
            $healthcareService->status === Status::INACTIVE ||
            $healthcareService->status === Status::DRAFT ||
            $healthcareService->status === Status::UNSYNCED
        ) {
            return Response::deny();
        }

        return Response::allow();
    }
}
