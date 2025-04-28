<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EpisodeRepository extends BaseRepository
{
    /**
     * Create episode for encounter in DB.
     *
     * @param  array  $data
     * @param  int  $encounterId
     * @return void
     * @throws Throwable
     */
    public function store(array $data, int $encounterId): void
    {
        DB::transaction(function () use ($data, $encounterId) {
            $repository = new Repository();

            try {
                $type = $repository::coding()->store($data['type']);
                $managingOrganization = $repository::identifier()->store($data['managing_organization']['identifier']['value']);
                $careManager = $repository::identifier()->store($data['care_manager']['identifier']['value']);

                $episode = $this->model::create([
                    'uuid' => $data['id'],
                    'encounter_id' => $encounterId,
                    'episode_type_id' => $type->id,
                    'status' => $data['status'],
                    'name' => $data['name'],
                    'managing_organization_id' => $managingOrganization->id,
                    'care_manager_id' => $careManager->id
                ]);

                $episode->period()->create([
                    'start' => $data['period']['start']
                ]);

                $repository::codeableConcept()->attach($managingOrganization, $data['managing_organization']);
                $repository::codeableConcept()->attach($careManager, $data['care_manager']);
            } catch (Exception $e) {
                Log::channel('db_errors')->error('Error saving episode', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get episode data that is related to the encounter.
     *
     * @param  int  $encounterId
     * @return array
     */
    public function get(int $encounterId): array
    {
        return $this->model::with([
            'type',
            'managingOrganization',
            'careManager'
        ])
            ->where('encounter_id', $encounterId)
            ->first()?->toArray();
    }
}
