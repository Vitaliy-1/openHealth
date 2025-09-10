<?php

namespace App\Repositories;


use Arr;
use Exception;
use App\Models\Division;
use App\Models\LegalEntity;
use App\Models\Relations\Phone;
use App\Models\Relations\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use App\Classes\eHealth\Api\Division as DivisionApi;

class DivisionRepository
{
    /**
     * Saves a list of divisions to the database.
     *
     * @param array $divisionsList The list of divisions to be saved
     * @param LegalEntity|null $legalEntity Optional legal entity associated with the divisions
     *
     * @return void
     */
    public function saveDivisionsList(array $divisionsList, ?LegalEntity $legalEntity = null): void
    {
        $legalEntity ??= legalEntity();

        DB::transaction(function() use($divisionsList, $legalEntity) {
            $uspertData = DivisionApi::normalizeResponseDataForUpsert($divisionsList, $legalEntity);

            // At first save all the Divisions to teh DB
            Division::upsert($uspertData, uniqueBy: ['uuid'], update: new Division()->getFillable());

            $divisionIds = $this->getDivisonListIds($divisionsList);

            $divisionsData = DivisionApi::getRelationshipData($divisionsList, $divisionIds);

            // Then set relations to the Address & Phone and save them to DB too
            $this->setSynDivisionsRelations($divisionsData);
        });
    }

    /**
     * Set status for specific action (for activate or deactivate)
     *
     * @param \App\Models\Division $division
     * @param string $status
     *
     * @return void
     *
     * @throws \Exception
     */
    public function setAction(Division $division, string $status): void
    {
        try {
            $division->setAttribute('status', $status)->save();

            $division->refresh();

        } catch (Exception $err) {
            throw new Exception($err->getMessage());
        }
    }

    /**
     * Create instance of Division cclass
     *
     * @param array $responseData // The data array suitable to do fill on Division Model
     *
     * @return Division|null
     */
    public function createOrUpdate(array $responseData): ?Division
    {
        $id = $responseData['id'] ?? null;
        $uuid= $responseData['uuid'] ?? null;

        Arr::forget($responseData, [
            'id',
            'phones',
            'addresses',
            'created_at',
            'updated_at',
            'legal_entity_uuid'
        ]);

        if (! $id && ! $uuid) {
            $division = new Division();
        } else {
            /*
             * At first try to get division by 'uuid'. But even if $uuid is present
             * the DB may contains record without DB (DRAFT case).
             * In that case the code below returns null
            */
            $division = $uuid ? Division::where('uuid', $uuid)->first() : null;

            // If $division is null, trying to get the record from the DB by 'id'
            $division ??= Division::find($id); // For the DRAFT records
        }

        if (! $division) {
            return null;
        }

        $division->fill($responseData);

        $division->setAttribute('external_id', $responseData['external_id'] ?? null);
        $division->setAttribute('status', $responseData['status']);

        return $division;
    }

    /**
     * Create instance of Division model and save it's data to the DB (with all it's relations aka: Address, Phone and LegalEntity)
     *
     * @param array $divisionData
     * @param \App\Models\LegalEntity $legalEntity
     *
     * @return ?Division
     */
    public function saveDivisionData(array $divisionData, LegalEntity $legalEntity): ?Division
    {
        $division = $this->createOrUpdate($divisionData);

        if (! $division) {
            return null;
        }

        $division = $this->createLegalEntityRelation($division, $legalEntity);

        $division->save();

        $division->refresh();

        Repository::address()->syncAddresses($division, $divisionData['addresses']);

        Repository::phone()->syncPhones($division, $divisionData['phones']);

        return $division;
    }

    /**
     * TODO: need more testing on further PRs
     * Create instance of Division model and save it's data to the DB (with all it's relations aka: Address, Phone and LegalEntity)
     *
     * @param array $divisionData
     * @param \App\Models\LegalEntity $legalEntity
     *
     * @return Division
     */
    public function syncDivisionData(array $divisionData, LegalEntity $legalEntity): Division
    {
        $division = $this->createOrUpdate($divisionData);

        if ($division->update()) {
            $division->refresh();
        }

        Repository::address()->syncAddresses($division, $divisionData['addresses']);

        Repository::phone()->syncPhones($division, $divisionData['phones']);

        return $division;
    }

    /**
     * Creates a relation between a Division and a LegalEntity
     *
     * @param Division $division The division to create the relation for
     * @param LegalEntity $legalEntity The legal entity to relate to the division
     *
     * @return Division Returns the updated Division instance
     */
    public function createLegalEntityRelation(Division $division, LegalEntity $legalEntity): Division
    {
        return $division->legalEntity()->associate($legalEntity);
    }

    /**
     * TODO: it suppose to be useful when do sync via jobs. If not, then just will remove it.
     * Creates a relation between a Division and such models as Employee, EmployeeRequest etc.
     *
     * @param Division $division The division entity to create relation for
     * @param Object $model The model to create relation with
     *
     * @return void
     */
    public function createRelationForDivision(Division $division, Object $model)
    {
        if (! $model) {
            return;
        }

        if ($model?->division_id === $division->id) {
            return;
        }

        $model->division()->associate($division);

        if (Schema::hasColumn($model->getTable(), 'division_uuid')) {
            $model->division_uuid = $division->uuid;
        }

        $model->save();
    }

    /**
     * Retrieves a collection of division IDs keyed by their UUIDs.
     *
     * This method takes a list of division data, extracts the UUIDs,
     * and queries the database to get the corresponding primary keys (IDs).
     *
     * @param array $divisionList An array of division data, where each element must contain a 'uuid' key.
     *
     * @return Collection A collection where keys are UUIDs and values are the corresponding division IDs.
     */
    public function getDivisonListIds(array $divisionList): Collection
    {
        $divisionUuids = array_column($divisionList, 'uuid');

        // Get ids of all the just inserted Divisions.
        return Division::whereIn('uuid', $divisionUuids)->pluck('id', 'uuid');
    }

    /**
     * Synchronizes address and phone relations for multiple divisions in bulk.
     *
     * This method performs a high-performance sync by first deleting all existing
     * address and phone records for the given divisions and then bulk-inserting
     * the new relational data. It is designed to be used within a transaction.
     *
     * @param array $dvisionsData An array containing the relational data.
     *                            Must include 'divisionIds', 'addresses', and 'phones' keys.
     * @return void
     */
    protected function setSynDivisionsRelations(array $dvisionsData): void
    {
        $divisionIds = $dvisionsData['divisionIds'];

        $addressesData = $dvisionsData['addresses'];

        $phonesData = $dvisionsData['phones'];

        // Remove all addresses records belongs to specified divisions
        Address::where('addressable_type', Division::class)
            ->whereIn('addressable_id', $divisionIds)
            ->delete();

        if (!empty($addressesData)) {
            Address::insert($addressesData);
        }

        // Remove all phones records belongs to specified divisions
        Phone::where('phoneable_type', Division::class)
            ->whereIn('phoneable_id', $divisionIds)
            ->delete();

        if (!empty($phonesData)) {
            Phone::insert($phonesData);
        }
    }
}
