<?php

declare(strict_types=1);

namespace App\Listeners\eHealth;

use App\Classes\eHealth\EHealth;
use App\Enums\Status;
use App\Events\EHealthUserLogin;
use Illuminate\Support\Facades\Log;

class ProcessNewEmployeeRequests extends BaseEmployeeListener
{
    /**
     * This listener should only process if the user's party is NOT yet synced (lacks a UUID).
     */
    protected function shouldProcess(EHealthUserLogin $event): bool
    {
        // The party must exist, but not have a UUID.
        return $event->user->party && !isset($event->user->party->uuid);
    }

    /**
     * Fetches employee data using the party's Tax ID and updates the party with the retrieved UUID,
     * prioritizing the party that is marked as 'VERIFIED' in the E-Health response.
     */
    protected function fetchEmployeesFromApi(EHealthUserLogin $event): array
    {
        $apiFilters = [
            'tax_id' => $event->user->party->tax_id,
            'legal_entity_id' => $event->legalEntity->uuid,
            'status' => Status::APPROVED->value,
        ];

        $response = EHealth::employee()->getMany($apiFilters);

        // Get the decoded data array from the response object first.
        $employees = $response->getData();

        if (!empty($employees)) {
            $verifiedPartyData = null;

            // First, search for an employee record linked to a VERIFIED party.
            foreach ($employees as $employee) {
                if (($employee['party']['verification_status'] ?? null) === 'VERIFIED') {
                    $verifiedPartyData = $employee['party'];
                    break; // Found the best candidate, no need to search further.
                }
            }

            // Use the verified party if found; otherwise, fall back to the first one available.
            $partyDataToUse = $verifiedPartyData ?? ($employees[0]['party'] ?? null);

            if ($partyDataToUse && isset($partyDataToUse['id'])) {
                // Instantly update the local party with the official UUID from the chosen party data.
                $event->user->party->update(['uuid' => $partyDataToUse['id']]);
                Log::info('Successfully updated party UUID from E-Health.', [
                    'party_id' => $event->user->party->id,
                    'chosen_party_uuid' => $partyDataToUse['id'],
                    'was_verified' => !is_null($verifiedPartyData)
                ]);
            }
        }

        return $response->validate();
    }

    protected function afterFetchingEmployees(EHealthUserLogin $event, array &$ehealthEmployees): void
    {
        if (empty($ehealthEmployees)) {
            return;
        }

        $verifiedPartyData = null;
        foreach ($ehealthEmployees as $employee) {
            if (($employee['party']['verification_status'] ?? null) === 'VERIFIED') {
                $verifiedPartyData = $employee['party'];
                break;
            }
        }

        $partyDataToUse = $verifiedPartyData ?? ($ehealthEmployees[0]['party'] ?? null);

        if ($partyDataToUse && isset($partyDataToUse['uuid'])) {
            $event->user->party->update(['uuid' => $partyDataToUse['uuid']]);
        }
    }
}
