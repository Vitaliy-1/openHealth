<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Encounter\CodeableConcept;
use App\Models\Encounter\Coding;
use App\Models\Encounter\Condition;
use App\Models\Encounter\Encounter;
use App\Models\Encounter\EncounterDiagnose;
use App\Models\Encounter\Episode;
use App\Models\Encounter\Identifier;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EncounterRepository
{
    /**
     * Create encounter in DB for person with related data.
     *
     * @param  array  $encounterData
     * @param  array  $episodeData
     * @param  int  $personId
     * @return false|int
     * @throws Throwable
     */
    public static function storeEncounter(array $encounterData, array $episodeData, int $personId): false|int
    {
        return DB::transaction(static function () use ($encounterData, $episodeData, $personId) {
            try {
                $visit = self::createIdentifier($encounterData['visit']['identifier']['value']);

                $episode = self::createIdentifier($encounterData['episode']['identifier']['value']);

                $class = self::createCoding($encounterData['class']);

                $type = self::createCodeableConcept($encounterData['type']);

                if (isset($encounterData['priority'])) {
                    $priority = self::createCodeableConcept($encounterData['priority']);
                }

                $performer = self::createIdentifier($encounterData['performer']['identifier']['value']);

                $division = self::createIdentifier($encounterData['division']['identifier']['value']);

                $encounter = Encounter::create([
                    'person_id' => $personId,
                    'uuid' => $encounterData['uuid'] ?? $encounterData['id'],
                    'status' => $encounterData['status'],
                    'visit_id' => $visit->id,
                    'episode_id' => $episode->id,
                    'class_id' => $class->id,
                    'type_id' => $type->id,
                    'priority_id' => $priority->id ?? null,
                    'performer_id' => $performer->id,
                    'division_id' => $division->id
                ]);

                $encounter->period()->create([
                    'start' => $encounterData['period']['start'],
                    'end' => $encounterData['period']['end']
                ]);

                self::attachCodeableConcept($visit, $encounterData['visit']);

                self::attachCodeableConcept($episode, $encounterData['episode']);

                self::storeEpisode($episodeData, $encounter->id);

                self::attachCodeableConcept($performer, $encounterData['performer']);

                $reasonIds = [];

                foreach ($encounterData['reasons'] as $reasonData) {
                    $reason = self::createCodeableConcept($reasonData);

                    $reasonIds[] = $reason->id;
                }

                $encounter->reasons()->attach($reasonIds);

                foreach ($encounterData['diagnoses'] as $diagnoseData) {
                    $condition = self::createIdentifier($diagnoseData['condition']['identifier']['value']);
                    self::attachCodeableConcept($condition, $diagnoseData['condition']);

                    $role = self::createCodeableConcept($diagnoseData['role']);

                    EncounterDiagnose::create([
                        'encounter_id' => $encounter->id,
                        'condition_id' => $condition->id,
                        'role_id' => $role->id,
                        'rank' => $diagnoseData['rank'] ?? null
                    ]);
                }

                $actionIds = [];

                foreach ($encounterData['actions'] as $actionData) {
                    $action = self::createCodeableConcept($actionData);

                    $actionIds[] = $action->id;
                }

                $encounter->actions()->attach($actionIds);

                self::attachCodeableConcept($division, $encounterData['division']);

                return $encounter->id;
            } catch (Exception $e) {
                Log::channel('db_errors')->error('Error saving encounter', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Store condition in DB.
     *
     * @param  array  $data
     * @param  int  $encounterId
     * @return void
     * @throws Throwable
     */
    public static function storeCondition(array $data, int $encounterId): void
    {
        DB::transaction(static function () use ($data, $encounterId) {
            try {
                foreach ($data as $datum) {
                    $reportOrigin = null;
                    $asserter = null;
                    $severity = null;

                    if (isset($datum['asserter'])) {
                        $asserter = self::createIdentifier($datum['asserter']['identifier']['value']);
                    }

                    $context = self::createIdentifier($datum['context']['identifier']['value']);

                    if (isset($datum['report_origin'])) {
                        $reportOrigin = self::createCodeableConcept($datum['report_origin']);
                    }

                    $code = self::createCodeableConcept($datum['code']);

                    if (isset($datum['severity'])) {
                        $severity = self::createCodeableConcept($datum['severity']);
                    }

                    Condition::create([
                        'uuid' => $datum['id'],
                        'encounter_id' => $encounterId,
                        'primary_source' => $datum['primary_source'],
                        'asserter_id' => $asserter?->id,
                        'report_origin_id' => $reportOrigin?->id,
                        'context_id' => $context->id,
                        'code_id' => $code->id,
                        'clinical_status' => $datum['clinical_status'],
                        'verification_status' => $datum['verification_status'],
                        'severity_id' => $severity?->id,
                        'onset_date' => $datum['onset_date'],
                        'asserted_date' => $datum['asserted_date'] ?? null
                    ]);

                    if (isset($datum['asserter'])) {
                        self::attachCodeableConcept($asserter, $datum['asserter']);
                    }

                    self::attachCodeableConcept($context, $datum['context']);
                }
            } catch (Exception $e) {
                Log::channel('db_errors')->error('Error saving condition', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);

                throw $e;
            }
        });
    }

    /**
     * Create episode for encounter in DB.
     *
     * @param  array  $data
     * @param  int  $encounterId
     * @return void
     * @throws Throwable
     */
    protected static function storeEpisode(array $data, int $encounterId): void
    {
        DB::transaction(static function () use ($data, $encounterId) {
            try {
                $type = self::createCoding($data['type']);

                $managingOrganization = self::createIdentifier($data['managing_organization']['identifier']['value']);

                $careManager = self::createIdentifier($data['care_manager']['identifier']['value']);

                $episode = Episode::create([
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

                self::attachCodeableConcept($managingOrganization, $data['managing_organization']);

                self::attachCodeableConcept($careManager, $data['care_manager']);
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
     * Get encounter data that is related to the patient.
     *
     * @param  int  $patientId
     * @return array
     */
    public static function getEncounterData(int $patientId): array
    {
        return Encounter::with([
            'period',
            'visit',
            'episode',
            'class',
            'type.coding',
            'priority.coding',
            'performer',
            'reasons.coding',
            'diagnoses',
            'actions.coding',
            'division'
        ])
            ->where('person_id', $patientId)
            ->first()?->toArray();
    }

    /**
     * Get condition data that is related to the encounter.
     *
     * @param  int  $encounterId
     * @return array
     */
    public static function getConditionData(int $encounterId): array
    {
        return Condition::with([
            'asserter',
            'reportOrigin.coding',
            'context',
            'code.coding',
            'severity.coding'
        ])
            ->where('encounter_id', $encounterId)
            ->get()->toArray();
    }

    /**
     * Get episode data that is related to the encounter.
     *
     * @param  int  $encounterId
     * @return array
     */
    public static function getEpisodeData(int $encounterId): array
    {
        return Episode::with([
            'type',
            'managingOrganization',
            'careManager'
        ])
            ->where('encounter_id', $encounterId)
            ->first()?->toArray();
    }

    /**
     * Create identifier in DB.
     *
     * @param  string  $value
     * @return Identifier
     */
    protected static function createIdentifier(string $value): Identifier
    {
        return Identifier::create(['value' => $value]);
    }

    /**
     * Create codeable concept in DB by provided data and attach coding.
     *
     * @param  array  $codeableConceptData
     * @return CodeableConcept
     */
    protected static function createCodeableConcept(array $codeableConceptData): CodeableConcept
    {
        $codeableConcept = CodeableConcept::create([
            'text' => $codeableConceptData['text'] ?? null
        ]);

        $codeableConcept->coding()->create([
            'system' => $codeableConceptData['coding'][0]['system'],
            'code' => $codeableConceptData['coding'][0]['code']
        ]);

        return $codeableConcept;
    }

    /**
     * Create codeable concept in DB for identifier.
     *
     * @param  Identifier  $identifier
     * @param  array  $codeableConceptData
     * @return CodeableConcept
     */
    protected static function attachCodeableConcept(Identifier $identifier, array $codeableConceptData): CodeableConcept
    {
        /** @var CodeableConcept $codeableConcept */
        $codeableConcept = $identifier->type()->create([
            'text' => $codeableConceptData['identifier']['type']['text'] ?? null
        ]);

        $codeableConcept->coding()->create([
            'system' => $codeableConceptData['identifier']['type']['coding'][0]['system'],
            'code' => $codeableConceptData['identifier']['type']['coding'][0]['code']
        ]);

        return $codeableConcept;
    }

    /**
     * Crate coding in DB by provided data.
     *
     * @param  array  $coding
     * @return Coding
     */
    protected static function createCoding(array $coding): Coding
    {
        return Coding::create([
            'system' => $coding['system'],
            'code' => $coding['code']
        ]);
    }
}
