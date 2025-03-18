<?php

declare(strict_types=1);

namespace App\Livewire\Encounter\Forms\Api;

use App\Classes\eHealth\Api\PersonApi;

class EncounterRequestApi extends PersonApi
{
    /**
     * Build an array of parameters for a service request list.
     *
     * @param  string  $requisition  A shared identifier common to all service requests that were authorized more or less simultaneously by a single author, representing the composite or group identifier. Example: AX654-654T.
     * @param  string  $status  The status of the service request. Default: active.
     * @param  int  $page  Page number. Default: 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 100. Default: 50.
     * @return array
     */
    public static function buildGetServiceRequestList(
        string $requisition,
        string $status = 'active',
        int $page = 1,
        int $pageSize = 50
    ): array {
        return [
            'requisition' => $requisition,
            'status' => $status,
            'page' => $page,
            'page_size' => $pageSize
        ];
    }

    /**
     * Build an array of parameters for a service request list.
     *
     * @param  int  $page  Page number. Default: 1.
     * @param  int  $pageSize  A limit on the number of objects to be returned, between 1 and 100. Default: 50.
     * @param  string|null  $code  Current diagnosis code. Example: R80.
     * @return array
     */
    public static function buildGetApprovedEpisodes(int $page = 1, int $pageSize = 50, ?string $code = null): array
    {
        return [
            'page' => $page,
            'page_size' => $pageSize,
            'code' => $code
        ];
    }

    /**
     * @param  int  $page
     * @param  int  $pageSize
     * @param  string|null  $code
     * @param  string|null  $encounterId
     * @param  string|null  $episodeId
     * @param  string|null  $onsetDateFrom
     * @param  string|null  $onsetDateTo
     * @param  string|null  $managingOrganizationId
     * @return array
     */
    public static function buildGetConditions(
        int $page = 1,
        int $pageSize = 50,
        ?string $code = null,
        ?string $encounterId = null,
        ?string $episodeId = null,
        ?string $onsetDateFrom = null,
        ?string $onsetDateTo = null,
        ?string $managingOrganizationId = null
    ): array {
        return [
            'page' => $page,
            'page_size' => $pageSize,
            'code' => $code,
            'encounter_id' => $encounterId,
            'episode_id' => $episodeId,
            'onset_date_from' => $onsetDateFrom,
            'onset_date_to' => $onsetDateTo,
            'managing_organization_id' => $managingOrganizationId
        ];
    }

    /**
     * Build an array of parameters for a dictionaries list.
     *
     * @param  string  $name  Dictionary name. Example: eHealth/ICF/classifiers
     * @param  bool|null  $isActive  Dictionary status. Example: true.
     * @param  string|null  $valueCode  Code of the dictionary value. Example: b1142.
     * @param  string|null  $valueDescription  Description of the dictionary value. Example: Орієнтація в особі.
     * @param  string|null  $valueIsActive  Status of the dictionary value. Example: true.
     * @return array
     */
    public static function buildGetDictionaries(
        string $name,
        ?bool $isActive = null,
        ?string $valueCode = null,
        ?string $valueDescription = null,
        ?string $valueIsActive = null
    ): array {
        return [
            'name' => $name,
            'is_active' => $isActive,
            'value_code' => $valueCode,
            'value_description' => $valueDescription,
            'value_is_active' => $valueIsActive
        ];
    }

    /**
     * Build an array of parameters for getting episodes using a search parameters list.
     *
     * @param  string|null  $periodFrom  Example: 2017-01-01.
     * @param  string|null  $periodTo  Example: 2018-01-01.
     * @param  string|null  $code  Example: R80.
     * @param  string|null  $status  Example: active.
     * @param  string|null  $managingOrganizationId  Example: 80a9e15b-b71b-4caf-8f2e-ff247e8a5677.
     * @param  int|null  $page  Page number. Default: 1.
     * @param  int|null  $pageSize  A limit on the number of objects to be returned, between 1 and 100. Default: 50.
     * @return array
     */
    public static function buildGetEpisodeBySearchParams(
        ?string $periodFrom = null,
        ?string $periodTo = null,
        ?string $code = null,
        ?string $status = null,
        ?string $managingOrganizationId = null,
        ?int $page = 1,
        ?int $pageSize = 50
    ): array {
        return [
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'code' => $code,
            'status' => $status,
            'managing_organization_id' => $managingOrganizationId,
            'page' => $page,
            'page_size' => $pageSize
        ];
    }
}
